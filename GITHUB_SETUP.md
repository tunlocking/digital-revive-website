## 🚀 GITHUB SETUP INSTRUCTIONS

Follow these steps to host your Digital Revive website on GitHub Pages:

---

### STEP 1: Create GitHub Account
If you don't have one:
- Go to https://github.com
- Click "Sign up"
- Complete registration

---

### STEP 2: Create a New Repository

**Option A: Using GitHub Website (Easiest)**
1. Go to https://github.com/new
2. Repository name: `digital-revive-website`
3. Description: "Professional repair service website for Digital Revive"
4. Choose "Public"
5. Click "Create repository"

**Option B: Using GitHub Desktop App**
1. Download GitHub Desktop from https://desktop.github.com
2. Click "Create a New Repository on your Hard Drive"
3. Name it: `digital-revive-website`
4. Point to `/Users/pc/Desktop/digital-revive-website`

---

### STEP 3: Connect Local Repository to GitHub

Open Terminal and run these commands:

```bash
cd /Users/pc/Desktop/digital-revive-website

# Replace USERNAME with your GitHub username
git remote add origin https://github.com/USERNAME/digital-revive-website.git

# Rename branch to main
git branch -M main

# Push to GitHub
git push -u origin main
```

When prompted, enter your GitHub credentials.

---

### STEP 4: Enable GitHub Pages

1. Go to your repository on GitHub (https://github.com/USERNAME/digital-revive-website)
2. Click "Settings" tab
3. Scroll left to "Pages" 
4. Under "Source", select:
   - Branch: `main`
   - Folder: `/ (root)`
5. Click "Save"

**Wait 1-2 minutes for GitHub to build your site.**

---

### STEP 5: Get Your Live Link

Your website will be live at:

**https://USERNAME.github.io/digital-revive-website**

Replace `USERNAME` with your actual GitHub username.

---

### QUICK COMMANDS SUMMARY

```bash
# First time setup (run once)
cd /Users/pc/Desktop/digital-revive-website
git remote add origin https://github.com/USERNAME/digital-revive-website.git
git branch -M main
git push -u origin main

# After making changes
git add .
git commit -m "Updated website content"
git push
```

---

### TROUBLESHOOTING

**Problem: "fatal: 'origin' does not appear to be a 'git' repository"**
- Run: `git remote -v` to check
- If empty, run the "First time setup" commands again

**Problem: Site not appearing after 5 minutes**
- Check Settings > Pages in your GitHub repo
- Make sure branch is set to "main" and folder is "/ (root)"
- Try accessing the link in an incognito window

**Problem: Want to use custom domain?**
- Buy a domain (GoDaddy, Namecheap, etc.)
- Go to Settings > Pages > Custom domain
- Enter your domain and follow instructions

---

### NEXT STEPS

1. **Customize content:**
   - Edit HTML files directly in VS Code
   - Update phone number, email, etc.
   - Add your own images to `/images` folder

2. **Update website after changes:**
   ```bash
   git add .
   git commit -m "Your change description"
   git push
   ```
   Changes appear live in 1-2 minutes!

3. **Add domain name (Optional):**
   - Buy domain from GoDaddy or similar
   - Add to GitHub Pages settings

---

### USEFUL GITHUB LINKS

- Your Repository: https://github.com/USERNAME/digital-revive-website
- GitHub Pages Docs: https://pages.github.com
- GitHub Desktop: https://desktop.github.com
- GitHub Mobile App: Available on App Store

---

**IMPORTANT:** Replace `USERNAME` with your actual GitHub username everywhere!

**Need help?** Contact Digital Revive at +212 638 038 932
