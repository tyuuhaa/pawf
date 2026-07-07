from django.urls import path

from .views import PostDetail, PostList, public_logout

urlpatterns = [
	path("", PostList.as_view(), name="home"),
	path("novel/<int:pk>/", PostDetail.as_view(), name="post_detail"),
	path("logout/", public_logout, name="logout"),
]
