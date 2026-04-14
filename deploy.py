#!/usr/bin/env python3
"""
Upload Goat Getter Website files to DreamHost via SFTP.

Uploads:
  - page-goat-getter-home.php → wp-content/themes/flavor/
  - assets/* → wp-content/themes/flavor/goat-getter-assets/
"""
import paramiko
import os
import sys
import warnings
warnings.filterwarnings('ignore')

HOST = "goat-getter.com"
USERNAME = "goatg"
PASSWORD = "Goat2024!!"
LOCAL_DIR = "/Users/jpage000/.gemini/antigravity/scratch/goat-getter-website"
REMOTE_DOMAIN_DIR = "goat-getter.com"
THEME = "hello-elementor"  # Active WP theme

def sftp_upload_item(sftp, local_path, remote_path):
    """Recursively upload a directory or file via SFTP."""
    if os.path.isdir(local_path):
        try:
            sftp.stat(remote_path)
        except:
            sftp.mkdir(remote_path)
            print(f"  📁 Created {remote_path}")

        for item in sorted(os.listdir(local_path)):
            if item in ('.git', '.DS_Store', '__pycache__', 'tests'):
                continue
            local_item = os.path.join(local_path, item)
            remote_item = remote_path + "/" + item
            sftp_upload_item(sftp, local_item, remote_item)
    else:
        print(f"  📄 {os.path.basename(local_path)} → {remote_path}")
        sftp.put(local_path, remote_path)

def main():
    print(f"\n🔗 Connecting to {HOST} via SFTP...")
    transport = paramiko.Transport((HOST, 22))
    transport.connect(username=USERNAME, password=PASSWORD)
    sftp = paramiko.SFTPClient.from_transport(transport)
    print("✅ Connected!")

    # Discover home dir
    home = sftp.normalize('.')
    remote_root = home + "/" + REMOTE_DOMAIN_DIR
    theme_root = remote_root + "/wp-content/themes/" + THEME
    print(f"📂 Theme root: {theme_root}")

    # 1. Upload PHP template
    print(f"\n▸ Uploading page template...")
    local_php = os.path.join(LOCAL_DIR, "page-goat-getter-home.php")
    remote_php = theme_root + "/page-goat-getter-home.php"
    try:
        sftp.put(local_php, remote_php)
        print(f"  ✅ page-goat-getter-home.php uploaded")
    except Exception as e:
        print(f"  ❌ Failed: {e}")

    # 2. Upload assets directory
    print(f"\n▸ Uploading assets to goat-getter-assets/...")
    local_assets = os.path.join(LOCAL_DIR, "assets")
    remote_assets = theme_root + "/goat-getter-assets"

    # Create the directory if needed
    try:
        sftp.stat(remote_assets)
    except:
        sftp.mkdir(remote_assets)
        print(f"  📁 Created {remote_assets}")

    sftp_upload_item(sftp, local_assets, remote_assets)
    print(f"  ✅ Assets uploaded")

    sftp.close()
    transport.close()
    print(f"\n═══════════════════════════════════════")
    print(f"✅ Goat Getter website deploy complete!")
    print(f"═══════════════════════════════════════")
    print(f"\nNext steps:")
    print(f"  1. Go to WP Admin → Pages → Edit front page")
    print(f"  2. Set Template to 'Goat Getter Home'")
    print(f"  3. Build Header/Footer in Elementor Theme Builder")
    print(f"  4. Flush permalinks: Settings → Permalinks → Save")
    print(f"\nView site: https://goat-getter.com/")

if __name__ == "__main__":
    main()
