<!-- plugins/markdowneditor/views/editor.blade.php -->

<h2>My Markdown Editor Plugin</h2>

<textarea id="markdown-editor" rows="10" cols="80"></textarea>

<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

<script>
  var simplemde = new SimpleMDE({ element: document.getElementById("markdown-editor") });
</script>
