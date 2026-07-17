# Publish Pikit Booking Widget to WordPress.org SVN
# Requires: TortoiseSVN (with command-line tools) OR any svn.exe on PATH
# Usage (from repo root):
#   .\scripts\svn-publish.ps1
# Optional:
#   .\scripts\svn-publish.ps1 -Username pikit -Version 1.0.1

param(
	[string]$Username = "pikit",
	[string]$Version = "1.0.1",
	[string]$SvnUrl = "https://plugins.svn.wordpress.org/pikit-widget",
	[string]$WorkDir = ""
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$PluginDir = Join-Path $Root "pikit-booking-widget"
$AssetsDir = Join-Path $Root "wordpress-org\assets"

if (-not $WorkDir) {
	$WorkDir = Join-Path $Root "svn-pikit-widget"
}

# Prefer TortoiseSVN CLI if installed
$svnCandidates = @(
	"svn",
	"C:\Program Files\TortoiseSVN\bin\svn.exe",
	"${env:ProgramFiles(x86)}\TortoiseSVN\bin\svn.exe"
)
$Svn = $null
foreach ($c in $svnCandidates) {
	if ($c -eq "svn") {
		$cmd = Get-Command svn -ErrorAction SilentlyContinue
		if ($cmd) { $Svn = $cmd.Source; break }
	} elseif (Test-Path $c) {
		$Svn = $c
		break
	}
}

if (-not $Svn) {
	Write-Error "svn.exe not found. Install TortoiseSVN with command-line tools, then reopen the terminal."
}

Write-Host "Using SVN: $Svn"
Write-Host "Plugin source: $PluginDir"
Write-Host "Working copy: $WorkDir"

# Validate version match
$main = Get-Content (Join-Path $PluginDir "pikit-booking-widget.php") -Raw
$readme = Get-Content (Join-Path $PluginDir "readme.txt") -Raw
if ($main -notmatch "Version:\s+$([regex]::Escape($Version))") {
	Write-Error "Plugin header Version does not match $Version"
}
if ($readme -notmatch "Stable tag:\s+$([regex]::Escape($Version))") {
	Write-Error "readme.txt Stable tag does not match $Version"
}

if (-not (Test-Path $WorkDir)) {
	Write-Host "Checking out $SvnUrl ..."
	& $Svn checkout $SvnUrl $WorkDir --username $Username
} else {
	Write-Host "Updating working copy..."
	& $Svn update $WorkDir --username $Username
}

$Trunk = Join-Path $WorkDir "trunk"
$Tags = Join-Path $WorkDir "tags"
$SvnAssets = Join-Path $WorkDir "assets"
New-Item -ItemType Directory -Force -Path $Trunk, $Tags, $SvnAssets | Out-Null

Write-Host "Syncing plugin files into trunk/ ..."
# Mirror plugin into trunk (exclude nothing critical; plugin folder is already clean)
Get-ChildItem $Trunk -Force -ErrorAction SilentlyContinue | ForEach-Object {
	Remove-Item $_.FullName -Recurse -Force
}
Copy-Item -Path (Join-Path $PluginDir "*") -Destination $Trunk -Recurse -Force

# Copy WP.org marketing assets if present
$assetFiles = @(
	"icon-128x128.png",
	"icon-256x256.png",
	"banner-772x250.png",
	"banner-1544x500.png",
	"screenshot-1.png",
	"screenshot-2.png",
	"screenshot-3.png",
	"screenshot-4.png"
)
foreach ($f in $assetFiles) {
	$src = Join-Path $AssetsDir $f
	if (Test-Path $src) {
		Copy-Item $src (Join-Path $SvnAssets $f) -Force
		Write-Host "  asset: $f"
	}
}

Push-Location $WorkDir
try {
	& $Svn status

	# Add new / missing files
	& $Svn add --force trunk
	& $Svn add --force assets

	Write-Host "Committing trunk + assets..."
	& $Svn commit -m "Release $Version: publish Pikit Booking Widget" --username $Username

	$TagPath = "tags/$Version"
	if (Test-Path (Join-Path $WorkDir $TagPath)) {
		Write-Host "Tag $Version already exists — skipping copy."
	} else {
		Write-Host "Creating tag $Version ..."
		& $Svn copy trunk $TagPath
		& $Svn commit -m "Tag version $Version" --username $Username
	}
} finally {
	Pop-Location
}

Write-Host ""
Write-Host "Done. Public page: https://wordpress.org/plugins/pikit-widget/"
Write-Host "If the page looks empty for a few minutes, wait — SVN sync can take up to ~15 minutes (search up to 72h)."
