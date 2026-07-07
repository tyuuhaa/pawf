from django.http import HttpResponseRedirect
from django.urls import include, path, re_path

from posts.admin import novel_admin_site


def legacy_admin_redirect(request, path=""):
	target = "/studio-novel/"
	if path:
		target += path
		if not target.endswith("/"):
			target += "/"
	query_string = request.META.get("QUERY_STRING")
	if query_string:
		target += f"?{query_string}"
	return HttpResponseRedirect(target)


urlpatterns = [
	path("studio-novel/", novel_admin_site.urls),
	path("admin/", legacy_admin_redirect),
	re_path(r"^admin/(?P<path>.*)$", legacy_admin_redirect),
	path("", include("posts.urls")),
]
