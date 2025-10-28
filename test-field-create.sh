#!/bin/bash

echo "Testing Field Create Form..."
echo ""
echo "Checking Laravel configuration..."
php artisan config:cache
php artisan view:clear
php artisan route:cache

echo ""
echo "Form should now work. Try creating a field with these values:"
echo "- Nama Lapangan: Lapangan Test"
echo "- Lokasi: Jakarta"
echo "- Deskripsi: Test lapangan"
echo "- Harga per Jam: 150000"
echo "- Status: Aktif (checked)"
echo ""
echo "Navigate to: http://localhost:8000/admin/fields/create"
