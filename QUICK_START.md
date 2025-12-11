# 🚀 QUICK START - Host on GitHub in 5 Minutes

## What You Need
- GitHub account (free) at https://github.com
- Terminal (already on your Mac)

---

## 3 Simple Steps

### Step 1️⃣ Create GitHub Repository
Go to https://github.com/new and fill in:
- Name: `digital-revive-website`
- Description: "Professional repair service website"
- Choose: Public
- Click: Create repository

### Step 2️⃣ Copy-Paste These Commands
Open Terminal and run (replace YOUR_USERNAME):

```bash
cd /Users/pc/Desktop/digital-revive-website

git remote add origin https://github.com/YOUR_USERNAME/digital-revive-website.git
git branch -M main
git push -u origin main
```

### Step 3️⃣ Enable GitHub Pages
On GitHub repo page:
1. Click Settings ⚙️
2. Click Pages (left sidebar)
3. Branch: main
4. Folder: / (root)
5. Save

---

## ✅ You're Done!

Your website is live at:

### 👉 https://YOUR_USERNAME.github.io/digital-revive-website

*Wait 2 minutes for GitHub to build your site*

---

## 📝 Make Changes Later

After editing files locally:

```bash
git add .
git commit -m "Updated content"
git push
```

Changes appear live in 1-2 minutes! ⚡

---

## 🎁 Bonus: Use GitHub Desktop App

Download from https://desktop.github.com for easier commits (no terminal needed!)

---

## 💡 Pro Tips

1. **Use a custom domain?**
   - Buy domain (GoDaddy.com, Namecheap.com)
   - Add in GitHub Settings > Pages > Custom domain
   - Update DNS settings at your domain provider

2. **Keep site updated?**
   - Edit HTML files in VS Code
   - Push with: `git add . && git commit -m "msg" && git push`
   - Site updates automatically!

3. **Need to test locally first?**
   ```bash
   python3 -m http.server 8000
   # Visit http://localhost:8000
   ```

---

**That's it! Your website is now on GitHub Pages!** 🎉

Questions? Email: info@digitalrevive.ma | Call: +212 638 038 932
