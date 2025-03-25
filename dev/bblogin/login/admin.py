from django.contrib import admin
from .models import Item, Transaction, Profile, EquivalenceTable, TradePost

admin.site.register(Item)
admin.site.register(Transaction)
admin.site.register(Profile)
admin.site.register(EquivalenceTable)
admin.site.register(TradePost)