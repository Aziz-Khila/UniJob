# UniLife Logo Guide

## Recommended Logo Specifications

### **File Formats**

1. **SVG (Scalable Vector Graphics)** - **RECOMMENDED**
   - Best for: Web use, scalable, small file size
   - Use when: You want one logo that works at all sizes
   - Preferred format for modern web applications

2. **PNG** - **FALLBACK OPTION**
   - Best for: High-quality images with transparency
   - Use when: SVG is not supported or you need specific pixel-perfect rendering
   - Recommended sizes: Create multiple sizes for different use cases

---

## Logo Sizes

### **Primary Logo (Main Usage)**
- **Size**: 200px × 60px (or similar aspect ratio like 3:1)
- **Format**: SVG (preferred) or PNG
- **Use**: Header/navigation, main branding
- **File name**: `logo.svg` or `logo.png`

### **Icon/App Icon (Square)**
- **Size**: 512px × 512px
- **Format**: PNG (with transparency)
- **Use**: Favicon, app icon, profile images
- **File name**: `icon-512.png` or `icon.png`

### **Favicon**
- **Size**: 32px × 32px (or 16px × 16px)
- **Format**: PNG or ICO
- **Use**: Browser tab icon
- **File name**: `favicon.png` or `favicon.ico`

### **Small/Compact Logo**
- **Size**: 120px × 40px
- **Format**: SVG or PNG
- **Use**: Mobile views, compact spaces
- **File name**: `logo-small.svg` or `logo-small.png`

### **Large/Hero Logo**
- **Size**: 400px × 120px
- **Format**: SVG or PNG
- **Use**: Landing pages, hero sections
- **File name**: `logo-large.svg` or `logo-large.png`

---

## Storage Location

### **Recommended Structure:**
```
public/
  └── images/
      └── logo/
          ├── logo.svg          (Primary logo - SVG)
          ├── logo.png          (Primary logo - PNG fallback)
          ├── logo-small.svg    (Compact version)
          ├── logo-large.svg    (Large version)
          ├── icon-512.png      (Square icon)
          └── favicon.png       (Favicon)
```

### **Simple Structure (if you only have one logo):**
```
public/
  └── images/
      ├── logo.svg    (or logo.png)
      └── favicon.png
```

---

## How to Use in Your Views

### **Option 1: Using asset() helper (Recommended)**
```blade
<img src="{{ asset('images/logo/logo.svg') }}" alt="UniLife Logo" />
```

### **Option 2: Using public_path() (if needed in PHP)**
```php
$logoPath = public_path('images/logo/logo.svg');
```

### **Example in Your Login/Register Pages:**
```blade
<div class="logo">
    <img src="{{ asset('images/logo/logo.svg') }}" alt="UniLife" style="height: 40px;" />
</div>
```

---

## Design Recommendations

### **Color Scheme** (Based on your current theme)
- Primary: Blue (#4a90e2) - University blue
- Secondary: Purple (#667eea, #764ba2) - Gradient colors
- Background: Dark (#1a1a2e) - Current dark theme
- Text: White/RGBA white for contrast

### **Logo Style Suggestions**
1. **Text-based logo**: "UniLife" in modern sans-serif font
2. **Icon + Text**: Simple icon (graduation cap, book, etc.) + "UniLife" text
3. **Monogram**: Stylized "UL" letters
4. **Abstract**: Geometric shape representing growth/learning

### **Design Tips**
- Keep it simple and recognizable
- Ensure it works on both light and dark backgrounds
- Make sure it's readable at small sizes (favicon)
- Use your university color scheme
- Consider creating both horizontal and vertical versions

---

## Quick Checklist

- [ ] Create logo in SVG format (primary)
- [ ] Export PNG versions for fallback (if needed)
- [ ] Create favicon (32x32 or 16x16)
- [ ] Create app icon (512x512 for future mobile apps)
- [ ] Test logo on dark background (current theme)
- [ ] Test logo at different sizes
- [ ] Place files in `public/images/logo/` folder
- [ ] Update views to use `{{ asset('images/logo/logo.svg') }}`

---

## Current Implementation

Once you create your logo, you can replace the text "UNILIFE" in:
- `resources/views/auth/login.blade.php` (line with `.logo` class)
- `resources/views/auth/register.blade.php` (line with `.logo` class)
- Any other views where you want the logo to appear

Replace this:
```blade
<div class="logo">UNILIFE</div>
```

With this:
```blade
<div class="logo">
    <img src="{{ asset('images/logo/logo.svg') }}" alt="UniLife" style="height: 40px; width: auto;" />
</div>
```





