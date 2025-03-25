from django.urls import path
from . import views

urlpatterns = [
    path('', views.homepage, name=""),
    path('register', views.register, name="register"),
    path('my-login', views.my_login, name="my-login"),
    path('dashboard', views.dashboard, name="dashboard"),
    path('user-logout', views.user_logout, name="user-logout"),









    path('item_list/', views.item_list, name='item_list'),
    path('item/<int:item_id>/', views.item_detail, name='item_detail'),
    path('post_trade/', views.post_trade, name='post_trade'),
    path('view_trades/', views.view_trades, name='view_trades'),
    path('request_trade/<int:trade_id>', views.request_trade, name='request_trade'),
    path('confirm_trade/<int:trade_id>/', views.confirm_trade, name='confirm_trade'),
    path('create_item/', views.create_item, name='create_item'),
]










