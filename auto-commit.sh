#!/bin/bash
echo "Menambahkan perubahan ke staging area..."
git add .

echo "Membuat commit otomatis..."
git commit -m "Auto commit: $(date)"

echo "Mengirim perubahan ke GitHub..."
git push origin main