$vendorPaths = @(
    'vendor/autoload.php',
    'vendor/barryvdh',
    'vendor/dompdf',
    'vendor/masterminds',
    'vendor/sabberworm',
    'vendor/composer'
)
$destZip = 'D:\apps\apps\deploy_update_csg_complete_with_vendor.zip'
if (Test-Path $destZip) { Remove-Item $destZip }

$tempDir = 'D:\apps\apps\civilservicegurukul.com\scratch\deploy_full_temp'
if (Test-Path $tempDir) { Remove-Item -Recurse -Force $tempDir }
New-Item -ItemType Directory -Path $tempDir | Out-Null

Expand-Archive -Path 'D:\apps\apps\deploy_update_csg_2026_10_11.zip' -DestinationPath $tempDir

foreach ($vp in $vendorPaths) {
    $src = Join-Path 'D:\apps\apps\civilservicegurukul.com' $vp
    $dst = Join-Path $tempDir $vp
    $dstDir = Split-Path $dst -Parent
    if (!(Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    Copy-Item $src $dst -Recurse -Force
}

Compress-Archive -Path "$tempDir\*" -DestinationPath $destZip -Force
Remove-Item -Recurse -Force $tempDir
Get-Item $destZip | Select-Object FullName, Length
