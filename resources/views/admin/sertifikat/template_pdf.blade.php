<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $nama }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: A4 landscape;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 100%;
            height: 100%;
        }
        .background-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .background-container img {
            width: 100%;
            height: 100%;
        }
        .content {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
        }
        .name-container {
            position: absolute;
            top: 39%;
            left: 5%;
            width: 90%;
            height: 12%; 
            display: table;
            text-align: center;
        }
        .name {
            display: table-cell;
            vertical-align: middle;
            font-size: {{ strlen($nama) > 30 ? '30pt' : (strlen($nama) > 20 ? '38pt' : '46pt') }};
            font-family: 'Helvetica-Bold', 'Helvetica', 'Arial', sans-serif;
            font-weight: bold;
            color: #021d7b;
            line-height: 1.1;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="background-container">
        @php
            $imagePath = 'images/sertifikat_batch3.jpg';
            $possiblePaths = [
                public_path($imagePath),
                base_path('public_html/' . $imagePath),
                base_path('public/' . $imagePath),
                '/home/u595896399/domains/beasiswamncu.com/public_html/' . $imagePath,
                public_path('images/sertifikat_batch2.jpg'),
                base_path('public_html/images/sertifikat_batch2.jpg'),
            ];
            
            $finalPath = null;
            foreach($possiblePaths as $path) {
                if(file_exists($path)) {
                    $finalPath = $path;
                    break;
                }
            }

            if($finalPath) {
                $type = pathinfo($finalPath, PATHINFO_EXTENSION);
                $data = file_get_contents($finalPath);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $base64 = '';
            }
        @endphp
        
        @if($base64)
            <img src="{{ $base64 }}" alt="Background">
        @endif
    </div>
    <div class="content">
        <div class="name-container">
            <div class="name">{{ $nama }}</div>
        </div>
    </div>
</body>
</html>
