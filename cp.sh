#!/bin/bash

echo "Creating upload package..."

# Create uploads directory if it doesn't exist
if [ ! -d "uploads" ]; then
    mkdir uploads
fi

# Clear existing files in uploads directory
rm -rf uploads/*

# Copy PHP template files
cp *.php uploads/

# Copy CSS files
cp -r css uploads/

# Copy JavaScript files
cp -r js uploads/

# Copy includes directory
cp -r inc uploads/

# Copy main style.css
cp style.css uploads/

# Copy screenshot
cp screenshot.png uploads/

echo "Upload package created in ./uploads/ directory"
echo ""
echo "Files copied:"
echo "- All PHP template files"
echo "- CSS directory"
echo "- JS directory"
echo "- INC directory"
echo "- style.css"
echo "- screenshot.png"
echo ""

# Create zip file
echo "Creating zip file..."
cd uploads
zip -r ../backbone-seo-llmo.zip * -q
cd ..

if [ -f "backbone-seo-llmo.zip" ]; then
    echo "✓ Zip file created: backbone-seo-llmo.zip"
    echo "  Size: $(du -h backbone-seo-llmo.zip | cut -f1)"
else
    echo "✗ Failed to create zip file"
fi

echo ""