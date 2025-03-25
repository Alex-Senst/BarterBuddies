
from django.contrib.auth.forms import UserCreationForm, AuthenticationForm
from django.contrib.auth.models import User
from . models import Profile

from django import forms

from django.forms.widgets import PasswordInput, TextInput

# Create/Register a user (Model Form)
class CreateUserForm(UserCreationForm):

    class Meta:

        model = User
        fields = ['username', 'email', 'password1', 'password2']


# Authenticate a user (Model Form)

class LoginForm(AuthenticationForm):

    username = forms.CharField(widget=TextInput())
    password = forms.CharField(widget=PasswordInput())


class ProfileForm(forms.ModelForm):
    class Meta:
        model = Profile
        fields = ['phone_number', 'address']



from django import forms
from .models import TradePost, Item
class TradePostForm(forms.ModelForm):
    class Meta:
        model = TradePost
        fields = ['item_offered', 'quantity', 'status']
    
    def init(self, *args, **kwargs):
        super(TradePostForm, self).init(*args, **kwargs)
        self.fields['item_offered'].queryset = Item.objects.all()

class ItemForm(forms.ModelForm):
    class Meta:
        model = Item
        fields = ['name', 'description']
