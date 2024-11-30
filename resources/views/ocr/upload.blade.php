<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>身份证 OCR 识别</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #a8c0ff, #3f2b96);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }
        .container {
            background: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
        }
        .language-select {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            padding: 5px 10px;
            z-index: 999;
        }
        .language-select select {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #ffffff;
            color: #333;
        }
        input[type="file"] {
            padding: 10px;
            margin-bottom: 20px;
            cursor: pointer;
            border: 1px solid #fff;
            border-radius: 5px;
            background-color: #00aaff;
            color: white;
            display: none; /* 隐藏原始文件选择框 */
        }
        .file-label {
            display: block;
            padding: 10px 20px;
            background-color: #00aaff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .file-label:hover {
            background-color: #007acc;
        }
        button {
            background-color: #00aaff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #007acc;
        }
        .image-preview {
            margin-top: 20px;
            max-width: 100%;
            max-height: 300px;
        }
    </style>
</head>
<body>

<!-- 语言选择框 -->
<div class="language-select">
    <label for="lang">选择语言：</label>
    <select name="lang" id="lang">
        <option value="chi_sim">简体中文</option>
        <option value="eng">英语</option>
    </select>
</div>

<div class="container">
    <h1>身份证 OCR 识别</h1>

    <!-- 文件上传表单 -->
    <form id="ocrForm" action="{{ route('ocr.process') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 显示的按钮标签 -->
        <label for="image" class="file-label">选择身份证图片</label>
        <input type="file" name="image" id="image" accept="image/*" required onchange="updateFileName(); previewImage();">

        <br><br>

        <button type="submit">提交识别</button>
    </form>

    <!-- 图像预览区域 -->
    <img id="image-preview" class="image-preview" src="" alt="Image preview" style="display:none;">
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 更新按钮文本为选中的文件名
    function updateFileName() {
        const fileInput = document.getElementById('image');
        const fileName = fileInput.files[0].name; // 获取文件名
        const fileLabel = document.querySelector('.file-label'); // 获取文件选择框标签
        fileLabel.textContent = fileName; // 更新按钮文本为文件名
    }

    // 显示图像预览
    function previewImage() {
        const fileInput = document.getElementById('image');
        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // 监听表单提交事件
    $('#ocrForm').on('submit', function(event) {
        event.preventDefault(); // 防止表单自动提交

        var formData = new FormData(this); // 获取表单数据

        // 使用 Ajax 提交表单
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false, // 不处理数据
            contentType: false, // 不设置 content-type
            success: function(response) {
                // 成功时弹出 OCR 识别文本
                if (response.text) {
                    Swal.fire({
                        title: 'OCR 识别成功',
                        html: formatOCRResult(response.text),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: '识别失败',
                        text: '未能识别文本，请重试。',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                // 错误时弹出提示
                Swal.fire({
                    title: '错误',
                    text: '发生错误: ' + error,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // 格式化 OCR 结果文本，替换 \n 为 <br> 实现换行
    function formatOCRResult(text) {
        return text.replace(/\n/g, '<br>');
    }

</script>

</body>
</html>
