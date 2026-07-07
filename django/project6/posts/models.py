from django.conf import settings
from django.core.validators import MaxValueValidator, MinValueValidator
from django.db import models
from django.urls import reverse


class Post(models.Model):
    class ReadingStatus(models.TextChoices):
        FINISHED = "selesai", "Selesai dibaca"
        READING = "membaca", "Sedang dibaca"
        WANT_TO_READ = "ingin", "Ingin dibaca"

    title = models.CharField("Judul novel", max_length=200, default="Novel tanpa judul")
    author = models.CharField("Penulis", max_length=120, default="Penulis belum diisi")
    genre = models.CharField("Genre", max_length=80, default="Umum")
    rating = models.PositiveSmallIntegerField(
        "Rating",
        default=5,
        validators=[MinValueValidator(1), MaxValueValidator(5)],
        help_text="Nilai 1 sampai 5",
    )
    status = models.CharField(
        "Status baca",
        max_length=12,
        choices=ReadingStatus.choices,
        default=ReadingStatus.FINISHED,
    )
    year_read = models.PositiveSmallIntegerField("Tahun dibaca", null=True, blank=True)
    review = models.TextField("Review", blank=True)
    favorite_quote = models.CharField("Kutipan favorit", max_length=255, blank=True)
    text = models.TextField("Catatan singkat", blank=True)
    created_by = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.CASCADE,
        related_name="novels",
        null=True,
        blank=True,
        verbose_name="Pemilik",
    )

    class Meta:
        verbose_name = "Novel"
        verbose_name_plural = "Daftar novel"
        ordering = ["-rating", "title"]

    def __str__(self):
        return f"{self.title} - {self.author}"

    @property
    def rating_stars(self):
        filled = "★" * self.rating
        empty = "☆" * (5 - self.rating)
        return filled + empty

    def get_absolute_url(self):
        return reverse("post_detail", kwargs={"pk": self.pk})

    @property
    def recommendation_score(self):
        score = self.rating * 20
        if self.status == self.ReadingStatus.FINISHED:
            score += 10
        if self.review:
            score += 5
        if self.favorite_quote:
            score += 5
        return min(score, 100)

    @property
    def recommendation_label(self):
        if self.recommendation_score >= 90:
            return "Wajib dibaca"
        if self.recommendation_score >= 75:
            return "Sangat direkomendasikan"
        if self.recommendation_score >= 60:
            return "Layak dibaca"
        return "Untuk eksplorasi"

    @property
    def reading_progress(self):
        return {
            self.ReadingStatus.FINISHED: 100,
            self.ReadingStatus.READING: 60,
            self.ReadingStatus.WANT_TO_READ: 20,
        }.get(self.status, 0)
