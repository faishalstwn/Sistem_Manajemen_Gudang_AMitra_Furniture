<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - A Mitra Furniture</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #eef6ff;
        }
        .sidebar {
            width: 220px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #ddd;
            padding: 16px 12px 16px 12px;
        }
        .sidebar a {
            display: block;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a.active,
        .sidebar a:hover {
            background: #e3f0ff;
            font-weight: bold;
        }
        .content {
            padding: 24px;
            width: 100%;
        }
        .card-box {
            background: #dbeafe;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    @include('admin.layout.sidebar')

    <div class="content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
