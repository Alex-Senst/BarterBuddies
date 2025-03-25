from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone


class Profile(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    phone_number = models.CharField(max_length=15, blank=True, null=True)
    address = models.CharField(max_length=255, blank=True, null=True)

    def __str__(self):
        return self.user.username





class Item(models.Model):
    name = models.CharField(max_length=100)
    description = models.TextField()
    posted_by = models.ForeignKey(Profile, on_delete=models.CASCADE)
    #quantity = models.PositiveIntegerField()
    #status = models.CharField(max_length=20, choices=[('open', 'Open'), ('completed', 'Completed'), ('canceled', 'Canceled')], default='open')

    def __str__(self):
        return self.name

class EquivalenceTable(models.Model):
    item_1 = models.ForeignKey(Item, related_name="item_1", on_delete=models.CASCADE)
    item_2 = models.ForeignKey(Item, related_name="item_2", on_delete=models.CASCADE)
    equivalence_percentage = models.DecimalField(max_digits=5, decimal_places=2)

    def __str__(self):
        return f'{self.item_1.name} : {self.item_2.name} = {self.equivalence_percentage}%'

class TradePost(models.Model):
    user = models.ForeignKey(Profile, on_delete=models.CASCADE)
    item_offered = models.ForeignKey(Item, related_name="item_offered", on_delete=models.CASCADE)
    #item_requested = models.ForeignKey(Item, related_name="item_requested", on_delete=models.CASCADE)
    quantity = models.PositiveIntegerField()
    #quantity_requested = models.PositiveIntegerField()
    status = models.CharField(max_length=20, choices=[('open', 'Open'), ('completed', 'Completed'), ('canceled', 'Canceled')], default='open')
    posted_at = models.DateTimeField(default=timezone.now)

    def __str__(self):
        return f'{self.user} offers {self.quantity} of {self.item_offered}'

class Transaction(models.Model):
    trade_post = models.ForeignKey(TradePost, on_delete=models.CASCADE)
    user_a = models.ForeignKey(Profile, related_name="user_a", on_delete=models.CASCADE)
    user_b = models.ForeignKey(Profile, related_name="user_b", on_delete=models.CASCADE)
    item_a = models.ForeignKey(Item, related_name="item_a", on_delete=models.CASCADE)
    item_b = models.ForeignKey(Item, related_name="item_b", on_delete=models.CASCADE)
    quantity_a = models.PositiveIntegerField()
    quantity_b = models.PositiveIntegerField()
    transaction_date = models.DateTimeField(auto_now_add=True)
    status = models.CharField(max_length=10, choices=[('pending', 'Pending'), ('completed', 'Completed')])

    def __str__(self):
        return f"Transaction between {self.user_a} and {self.user_b} for {self.quantity_a} of {self.item_a} and {self.quantity_b} of {self.item_b}"
    
    def calculate_final_values(self):
        equivalence = EquivalenceTable.objects.get(item_1=self.item_a, item_2=self.item_b)
        equivalence_percentage = equivalence.equivalence_percentage / 100.0
        self.quantity_b = self.quantity_a * equivalence_percentage  # Adjust quantity of item_b based on equivalence percentage
        self.save()

