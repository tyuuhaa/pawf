from django.contrib import admin
from django.contrib.admin import AdminSite
from django.contrib.auth.admin import UserAdmin
from django.contrib.auth.models import User
from django.core.exceptions import PermissionDenied
from django.db.models import Avg
from django.urls import path, reverse
from django.views.generic import RedirectView

from .models import Post


class NovelAdminSite(AdminSite):
    site_header = "Aplikasi Novel Admin"
    site_title = "Aplikasi Novel"
    index_title = "Ruang kendali novel dan akun"
    site_url = "/"
    index_template = "admin/studio_index.html"

    def get_urls(self):
        urls = super().get_urls()
        user_admin = self._registry[User]
        post_admin = self._registry[Post]
        custom_urls = [
            path("auth/group/", RedirectView.as_view(url="/studio-novel/")),
            path("auth/group/add/", RedirectView.as_view(url="/studio-novel/")),
            path("auth/group/<path:object_id>/change/", RedirectView.as_view(url="/studio-novel/")),
            path("auth/user/", RedirectView.as_view(url="/studio-novel/akun/pengguna/")),
            path("auth/user/add/", RedirectView.as_view(url="/studio-novel/akun/pengguna/tambah/")),
            path(
                "auth/user/<path:object_id>/change/",
                RedirectView.as_view(url="/studio-novel/akun/pengguna/%(object_id)s/edit/"),
            ),
            path("posts/post/", RedirectView.as_view(url="/studio-novel/novel/")),
            path("posts/post/add/", RedirectView.as_view(url="/studio-novel/novel/tambah/")),
            path(
                "posts/post/<path:object_id>/change/",
                RedirectView.as_view(url="/studio-novel/novel/%(object_id)s/edit/"),
            ),
            path("akun/pengguna/", self.admin_view(user_admin.changelist_view), name="studio_user_changelist"),
            path("akun/pengguna/tambah/", self.admin_view(user_admin.add_view), name="studio_user_add"),
            path(
                "akun/pengguna/<path:object_id>/edit/",
                self.admin_view(user_admin.change_view),
                name="studio_user_change",
            ),
            path("novel/", self.admin_view(post_admin.changelist_view), name="studio_post_changelist"),
            path("novel/tambah/", self.admin_view(post_admin.add_view), name="studio_post_add"),
            path(
                "novel/<path:object_id>/edit/",
                self.admin_view(post_admin.change_view),
                name="studio_post_change",
            ),
        ]
        return custom_urls + urls

    def get_app_list(self, request):
        app_list = super().get_app_list(request)
        route_prefix = f"{self.name}:"
        for app in app_list:
            if app["app_label"] == "auth":
                app["name"] = "Akun & Akses"
                app["app_url"] = reverse(f"{route_prefix}studio_user_changelist")
                for model in app["models"]:
                    if model["object_name"] == "User":
                        model["admin_url"] = reverse(f"{route_prefix}studio_user_changelist")
                        model["add_url"] = reverse(f"{route_prefix}studio_user_add")
            elif app["app_label"] == "posts":
                app["name"] = "Novel & Rating"
                app["app_url"] = reverse(f"{route_prefix}studio_post_changelist")
                for model in app["models"]:
                    if model["object_name"] == "Post":
                        model["admin_url"] = reverse(f"{route_prefix}studio_post_changelist")
                        model["add_url"] = reverse(f"{route_prefix}studio_post_add")
        return app_list

    def index(self, request, extra_context=None):
        extra_context = extra_context or {}
        queryset = Post.objects.all()
        if not request.user.is_superuser:
            queryset = queryset.filter(created_by=request.user)
        rating_average = queryset.aggregate(avg=Avg("rating"))["avg"] or 0
        extra_context.update(
            {
                "dashboard_stats": {
                    "total_novels": queryset.count(),
                    "finished_books": queryset.filter(status=Post.ReadingStatus.FINISHED).count(),
                    "reading_now": queryset.filter(status=Post.ReadingStatus.READING).count(),
                    "want_to_read": queryset.filter(status=Post.ReadingStatus.WANT_TO_READ).count(),
                    "average_rating": round(rating_average, 1),
                    "active_books": queryset.filter(
                        status__in=[Post.ReadingStatus.READING, Post.ReadingStatus.WANT_TO_READ]
                    ).count(),
                },
                "recent_novels": queryset.select_related("created_by").order_by("-id")[:5],
            }
        )
        return super().index(request, extra_context=extra_context)


novel_admin_site = NovelAdminSite(name="admin")


class PostAdmin(admin.ModelAdmin):
    list_display = ("title", "author", "genre", "status", "rating_display", "year_read", "created_by_display")
    list_filter = ("status", "genre", "rating", "year_read")
    search_fields = ("title", "author", "genre", "review", "favorite_quote")
    ordering = ("-rating", "title")
    list_per_page = 10
    save_on_top = True
    change_list_template = "admin/posts/post/change_list.html"
    change_form_template = "admin/posts/post/change_form.html"
    fieldsets = (
        ("Informasi novel", {"fields": ("title", "author", "genre", "created_by")}),
        ("Penilaian", {"fields": ("rating", "status", "year_read")}),
        ("Isi dan catatan", {"fields": ("review", "favorite_quote", "text")}),
    )

    def get_queryset(self, request):
        queryset = super().get_queryset(request)
        if request.user.is_superuser:
            return queryset
        return queryset.filter(created_by=request.user)

    def has_module_permission(self, request):
        return request.user.is_active and request.user.is_staff

    def has_view_permission(self, request, obj=None):
        if request.user.is_superuser:
            return True
        if obj is None:
            return request.user.has_perm("posts.view_post")
        return obj.created_by_id == request.user.id

    def has_add_permission(self, request):
        return request.user.is_superuser or request.user.has_perm("posts.add_post")

    def has_change_permission(self, request, obj=None):
        if request.user.is_superuser:
            return True
        if obj is None:
            return request.user.has_perm("posts.change_post")
        return obj.created_by_id == request.user.id

    def has_delete_permission(self, request, obj=None):
        return request.user.is_superuser

    def save_model(self, request, obj, form, change):
        if not request.user.is_superuser and obj.pk:
            current = Post.objects.filter(pk=obj.pk).first()
            if current and current.created_by_id != request.user.id:
                raise PermissionDenied("Hanya data sendiri yang dapat diubah.")
        if obj.created_by_id is None:
            obj.created_by = request.user
        super().save_model(request, obj, form, change)

    def get_readonly_fields(self, request, obj=None):
        if request.user.is_superuser:
            return ()
        return ("created_by",)

    def get_list_display(self, request):
        if request.user.is_superuser:
            return self.list_display
        return tuple(field for field in self.list_display if field != "created_by_display")

    def changelist_view(self, request, extra_context=None):
        extra_context = extra_context or {}
        queryset = self.get_queryset(request)
        extra_context.update(
            {
                "total_books": queryset.count(),
                "finished_books": queryset.filter(status=Post.ReadingStatus.FINISHED).count(),
                "reading_now": queryset.filter(status=Post.ReadingStatus.READING).count(),
                "strong_recommendations": queryset.filter(rating__gte=4).count(),
            }
        )
        return super().changelist_view(request, extra_context=extra_context)

    @admin.display(description="Rating")
    def rating_display(self, obj):
        return obj.rating_stars

    @admin.display(description="Pemilik")
    def created_by_display(self, obj):
        return obj.created_by.username if obj.created_by else "-"


novel_admin_site.register(Post, PostAdmin)
novel_admin_site.register(User, UserAdmin)

novel_admin_site._registry[User].change_form_template = "admin/auth/user/change_form.html"
novel_admin_site._registry[User].change_list_template = "admin/auth/user/change_list.html"
novel_admin_site._registry[User].add_form_template = "admin/auth/user/add_form.html"
