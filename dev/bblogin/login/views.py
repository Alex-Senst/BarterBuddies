from django.shortcuts import render, redirect,     get_object_or_404
from . forms import CreateUserForm, LoginForm, ProfileForm,       TradePostForm, ItemForm
from django.contrib.auth.decorators import login_required
from django.http import HttpResponseRedirect

from . models import Item, TradePost, Transaction, Profile


# Authentication models and fucntions
from django.contrib.auth.models import auth
from django.contrib.auth import authenticate, login, logout

def homepage(request):
    return render(request, 'login/index.html')

def register(request):
    
    form = CreateUserForm()
    profile_form = ProfileForm(request.POST)

    if request.method == "POST":
        form = CreateUserForm(request.POST)

        if form.is_valid() and profile_form.is_valid():
            user = form.save()

            profile = profile_form.save(commit=False)
            profile.user = user
            profile.save()

            return redirect("my-login")

    context = {'registerform' : form, 'profileform': profile_form}
    
    
    return render(request, 'login/register.html', context=context)

def my_login(request):

    form = LoginForm()

    next_url = request.GET.get('next', 'dashboard')

    if request.method == 'POST':
        form=LoginForm(request, data=request.POST)

        if form.is_valid():
            username = request.POST.get('username')
            password = request.POST.get('password')

            user = authenticate(request, username=username, password=password)

            if user is not None:
                auth.login(request, user)
                return redirect(next_url)
    
    context = {'loginform' : form}

    return render(request, 'login/my-login.html', context=context)

@login_required(login_url="my-login")
def dashboard(request):
    profile = Profile.objects.get(user=request.user)  # Get the logged-in user's profile
    items = Item.objects.filter(posted_by=profile)  # Filter items by the logged-in user
    return render(request, 'login/dashboard.html', {'items': items})
    #items = Item.objects.all()
    #return render(request, 'login/dashboard.html', {'items':items})
    #return render(request, 'login/dashboard.html')

def user_logout(request):
    auth.logout(request)

    return redirect("")











def item_list(request):
    items = Item.objects.all()
    return render(request, 'login/dashboard.html', {'items':items})

def item_detail(request, item_id):
    item = get_object_or_404(Item, pk=item_id)
    trade = TradePost.objects.filter(item_offered=item).order_by('-posted_at').first()  # Get the latest trade
    return render(request, 'login/item_detail.html', {'item': item, 'trade': trade})

@login_required(login_url="my-login")
def post_trade(request):
    if request.method == 'POST':
        form = TradePostForm(request.POST)
        if form.is_valid():
            try:
                profile = Profile.objects.get(user=request.user)
            except Profile.DoesNotExist:
                # Handle the case where the profile does not exist
                messages.error(request, "You need to complete your profile before posting a trade.")
                return redirect('profile')  # Redirect the user to complete their profile
            trade = form.save(commit=False)
            trade.user = profile # Associate the trade with the logged-in user
            trade.save()
            return redirect('dashboard')  # Redirect to the dashboard after successful trade submission
    else:
        form = TradePostForm()
    
    return render(request, 'login/post_trade.html', {'form': form})

@login_required(login_url="my-login")
def view_trades(request):
    open_trades = TradePost.objects.filter(status='open')  # Get all open trade posts
    return render(request, 'login/view_trades.html', {'open_trades': open_trades})

@login_required(login_url="my-login")
def request_trade(request, trade_id):
    trade_post = get_object_or_404(TradePost, pk=trade_id)
    
    if request.method == 'POST':
        # Logic for processing trade request, including checking equivalence and costs
        # Example: create a transaction between the user requesting the trade and the post owner
        transaction = Transaction.objects.create(
            trade_post=trade_post,
            user_a=request.user,  # The user making the request
            user_b=trade_post.user,  # The user who posted the trade
            item_a=trade_post.item_offered,  # Item A (offered by the requester)
            item_b=trade_post.item_requested,  # Item B (requested by the requester)
            quantity_a=trade_post.quantity_offered,
            quantity_b=trade_post.quantity_requested,
            status='pending',  # The transaction is pending until both parties confirm
        )
        return redirect('confirm_trade', trade_id=transaction.id)
    
    return render(request, 'login/request_trade.html', {'trade_post': trade_post})


@login_required(login_url="my-login")
def confirm_trade(request, trade_id):
    transaction = get_object_or_404(Transaction, pk=trade_id)
    
    # Implement logic to confirm the trade (e.g., check equivalence, handle costs, etc.)
    if transaction.status == 'pending':
        # Logic for trade completion, updating inventory, etc.
        transaction.status = 'completed'
        transaction.save()
        
        # You can add logic here for actual item exchanges, such as updating item quantities
        # This may include ensuring both items are exchanged and reducing quantities accordingly
        
        return redirect('dashboard')  # Redirect to a list of completed trades or something appropriate
    
    return render(request, 'login/confirm_trade.html', {'transaction': transaction})

@login_required(login_url="my-login")
def create_item(request):
    if request.method == 'POST':
        form = ItemForm(request.POST)
        if form.is_valid():
            # Retrieve the user's profile instance
            profile = Profile.objects.get(user=request.user)
            
            # Create the item, and assign the posted_by field
            item = form.save(commit=False)
            item.posted_by = profile
            item.save()
            return redirect('post_trade')  # Redirect after saving the item
    else:
        form = ItemForm()

    return render(request, 'login/create_item.html', {'form': form})

