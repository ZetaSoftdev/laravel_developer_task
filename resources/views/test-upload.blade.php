<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test File Upload</title>
</head>
<body>
    <h1>Test File Upload</h1>
    
    <form action="{{ url('/test-file-upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="test_file">Select a file:</label>
            <input type="file" name="test_file" id="test_file">
        </div>
        <button type="submit">Upload</button>
    </form>

    <h2>Current Configuration</h2>
    <pre>
        Upload Max Filesize: {{ ini_get('upload_max_filesize') }}
        Post Max Size: {{ ini_get('post_max_size') }}
        Upload Temp Dir: {{ ini_get('upload_tmp_dir') ?: 'Not set' }}
        System Temp Dir: {{ sys_get_temp_dir() }}
        Is Writable: {{ is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) ? 'Yes' : 'No' }}
    </pre>
</body>
</html> 