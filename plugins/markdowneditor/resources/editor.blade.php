<!DOCTYPE html>
<html>
<head>
    <title>Markdown Editor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Markdown Editor</h1>
    <form id="markdown-form">
        <textarea name="markdown" rows="10" cols="80"></textarea>
        <br>
        <button type="submit">Preview</button>
    </form>
    <div id="preview" style="border:1px solid #ccc; margin-top:20px; padding:10px;"></div>

    <script>
        document.getElementById('markdown-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const markdown = e.target.elements.markdown.value;

            fetch("{{ route('markdowneditor.parse') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ markdown })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('preview').innerHTML = data.html;
            });
        });
    </script>
</body>
</html>
