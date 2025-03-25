# Generated by Django 5.1.7 on 2025-03-25 17:55

import django.utils.timezone
from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('login', '0004_item_status'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='item',
            name='quantity',
        ),
        migrations.RemoveField(
            model_name='item',
            name='status',
        ),
        migrations.AddField(
            model_name='tradepost',
            name='posted_at',
            field=models.DateTimeField(default=django.utils.timezone.now),
        ),
    ]
