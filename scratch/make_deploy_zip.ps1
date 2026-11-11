$destZip = 'D:\apps\apps\deploy_update_csg_2026_10_11.zip'
if (Test-Path $destZip) { Remove-Item $destZip }

$files = @(
    'composer.json',
    'composer.lock',
    'bootstrap/providers.php',
    'routes/web.php',
    'app/Models/Article.php',
    'app/Models/CourseBatch.php',
    'app/Models/CourseLesson.php',
    'app/Http/Controllers/Admin/ArticleController.php',
    'app/Http/Controllers/Admin/CourseController.php',
    'app/Http/Controllers/Public/ArticleController.php',
    'app/Http/Controllers/Public/CourseController.php',
    'app/Http/Controllers/Student/DashboardController.php',
    'database/migrations/2026_10_11_140000_add_pdf_fields_to_articles_table.php',
    'database/migrations/2026_10_11_143000_add_planner_and_interactive_fields_to_course_lessons_table.php',
    'resources/views/admin/articles/_pdf_upload_field.blade.php',
    'resources/views/admin/articles/create.blade.php',
    'resources/views/admin/articles/edit.blade.php',
    'resources/views/admin/courses/edit.blade.php',
    'resources/views/admin/courses/create.blade.php',
    'resources/views/public/articles/pdf.blade.php',
    'resources/views/public/articles/show.blade.php',
    'resources/views/public/courses/show.blade.php',
    'resources/views/student/courses/show.blade.php'
)

$tempDir = 'D:\apps\apps\civilservicegurukul.com\scratch\deploy_temp'
if (Test-Path $tempDir) { Remove-Item -Recurse -Force $tempDir }
New-Item -ItemType Directory -Path $tempDir | Out-Null

foreach ($f in $files) {
    $src = Join-Path 'D:\apps\apps\civilservicegurukul.com' $f
    $dst = Join-Path $tempDir $f
    $dstDir = Split-Path $dst -Parent
    if (!(Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    Copy-Item $src $dst -Force
}

Compress-Archive -Path "$tempDir\*" -DestinationPath $destZip -Force
Remove-Item -Recurse -Force $tempDir

Get-Item $destZip | Select-Object FullName, Length
