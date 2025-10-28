import re
import yaml
import subprocess
from pathlib import Path

# Base path: script is two levels below repo root
repo_root = Path(__file__).resolve().parent.parent.parent

# Paths relative to repo root
dockerfile_path = repo_root / "Dockerfile"
workflow_path = repo_root / ".github/workflows/php.yml"
readme_path = repo_root / "README.md"

# Read Dockerfile
dockerfile = dockerfile_path.read_text()

# Extract PHP image
php_image_match = re.search(r'^FROM (php:[^\s]+)-cli-alpine', dockerfile, re.MULTILINE)
php_image = php_image_match.group(1) if php_image_match else None
print(f'Found PHP image: `{php_image}`')

# Extract Composer image
composer_image_match = re.search(r'^FROM (composer:[^\s]+) AS composer', dockerfile, re.MULTILINE)
composer_image = composer_image_match.group(1) if composer_image_match else None
print(f'Found Composer image: `{composer_image}`')

# Get versions by running Docker containers
def get_php_version(image):
    result = subprocess.run(
        ["docker", "run", "--rm", image, "php", "-r", "echo PHP_VERSION;"],
        capture_output=True, text=True
    )
    return result.stdout.strip()

def get_alpine_version(image):
    result = subprocess.run(
        ["docker", "run", "--rm", image, "cat", "/etc/alpine-release"],
        capture_output=True, text=True
    )
    return result.stdout.strip()

def get_composer_version(image):
    result = subprocess.run(
        ["docker", "run", "--rm", image, "composer", "--version"],
        capture_output=True, text=True
    )
    # Output is like: Composer version 2.8.12 2023-03-12 14:38:00
    return result.stdout.strip().split()[2]

print(f'Starting docker php image to inspect')
php_ver = get_php_version(php_image) if php_image else "unknown"
print(f'PHP Version: {php_ver}')
print(f'Starting docker php image to inspect')
alpine_ver = get_alpine_version(f"{php_image}-alpine") if php_image else "unknown"
print(f'Alpine Linux Version: {alpine_ver}')
print(f'Starting docker Composer image to inspect')
composer_ver = get_composer_version(composer_image) if composer_image else "unknown"
print(f'Composer Version: {composer_ver}')

# Read workflow YAML for action versions
workflow = yaml.safe_load(workflow_path.read_text())
action_versions = {}
for step in workflow.get('jobs', {}).get('build', {}).get('steps', []):
    uses = step.get('uses')
    if uses:
        action_name, version = uses.split('@')
        action_versions[action_name] = version
        print(f'{action_name} Version: {version}')

# Update README badges
print(f'Updating README badges')
readme = readme_path.read_text()
readme = re.sub(r'!\[PHP Version\]\(.*?\)', f'![PHP Version](https://img.shields.io/badge/PHP-v{php_ver}-blue)', readme)
readme = re.sub(r'!\[Alpine Linux Version\]\(.*?\)', f'![Alpine Linux Version](https://img.shields.io/badge/Alpine_Linux-v{alpine_ver}-blue)', readme)
readme = re.sub(r'!\[Composer Version\]\(.*?\)', f'![Composer Version](https://img.shields.io/badge/Composer-v{composer_ver}-blue)', readme)

# Update bullets with GitHub Actions versions
print(f'Updating README bullets with GitHub Actions versions')
for step in workflow.get('jobs', {}).get('build', {}).get('steps', []):
    uses = step.get('uses')
    name = step.get('name')
    if uses and name:
        action_name = uses.split('@')[0]
        version = uses.split('@')[1]
        readme = re.sub(
            rf'(``{re.escape(action_name)}@[^`]+``)',
            f'``{action_name}@{version}``',
            readme
        )

# Save updated README
readme_path.write_text(readme)