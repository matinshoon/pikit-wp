# Commit the already-staged SVN working copy (trunk + assets + tag).
# Run from repo root after svn-pikit-widget/ has been prepared.
# You will be prompted for the WordPress.org SVN password for user "pikit".

param(
	[string]$Username = "pikit",
	[string]$Version = "1.0.1"
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$WorkDir = Join-Path $Root "svn-pikit-widget"
$Svn = "C:\Program Files\TortoiseSVN\bin\svn.exe"
if (-not (Test-Path $Svn)) {
	$Svn = (Get-Command svn).Source
}

if (-not (Test-Path $WorkDir)) {
	Write-Error "Missing $WorkDir — run .\scripts\svn-publish.ps1 first."
}

Set-Location $WorkDir

Write-Host "Committing trunk + assets as $Username ..."
& $Svn commit -m "Release $Version: publish Pikit Booking Widget" --username $Username
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

$TagPath = "tags/$Version"
if (Test-Path (Join-Path $WorkDir $TagPath)) {
	Write-Host "Tag $Version already exists."
} else {
	Write-Host "Creating tag $Version ..."
	& $Svn copy trunk $TagPath
	& $Svn commit -m "Tag version $Version" --username $Username
	if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

Write-Host ""
Write-Host "Live: https://wordpress.org/plugins/pikit-widget/"
