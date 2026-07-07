from django.contrib.auth import logout
from django.shortcuts import redirect
from django.db.models import Avg, Q
from django.views.generic import DetailView, ListView

from .models import Post


class PostList(ListView):
    model = Post
    template_name = "post_list.html"

    def get_queryset(self):
        queryset = super().get_queryset()
        query = self.request.GET.get("q")
        status = self.request.GET.get("status")
        min_rating = self.request.GET.get("min_rating")

        if query:
            queryset = queryset.filter(
                Q(title__icontains=query) | Q(author__icontains=query) | Q(genre__icontains=query)
            )

        if status:
            queryset = queryset.filter(status=status)
        if min_rating:
            queryset = queryset.filter(rating__gte=min_rating)

        return queryset

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        queryset = self.get_queryset()
        user = self.request.user
        context["query"] = self.request.GET.get("q", "")
        context["selected_status"] = self.request.GET.get("status", "")
        context["selected_min_rating"] = self.request.GET.get("min_rating", "")
        context["total_books"] = Post.objects.count()
        context["finished_books"] = Post.objects.filter(status=Post.ReadingStatus.FINISHED).count()
        context["average_rating"] = queryset.aggregate(avg_rating=Avg("rating"))["avg_rating"]
        context["top_pick"] = Post.objects.order_by("-rating", "title").first()
        context["strong_recommendations"] = Post.objects.filter(rating__gte=4, status=Post.ReadingStatus.FINISHED).count()
        context["reading_now"] = Post.objects.filter(status=Post.ReadingStatus.READING).count()
        context["my_books"] = Post.objects.filter(created_by=user).order_by("-rating", "title") if user.is_authenticated else Post.objects.none()
        context["my_books_count"] = context["my_books"].count() if user.is_authenticated else 0
        context["my_latest_book"] = context["my_books"].first() if user.is_authenticated else None
        return context


class PostDetail(DetailView):
    model = Post
    template_name = "post_detail.html"

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        post = self.object
        context["recommendation_label"] = post.recommendation_label
        context["recommendation_score"] = post.recommendation_score
        context["reading_progress"] = post.reading_progress
        context["similar_posts"] = (
            Post.objects.exclude(pk=post.pk)
            .filter(Q(genre=post.genre) | Q(rating__gte=max(post.rating - 1, 1)))
            .order_by("-rating", "title")[:3]
        )
        return context


def public_logout(request):
    logout(request)
    return redirect("home")
