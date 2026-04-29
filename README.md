Project ini merupakan RESTful API berbasis Laravel 13 yang memiliki beberapa fitur utama sebagai berikut:

✅ Autentikasi berbasis token dengan memanfaatkan Laravel Sanctum
✅ Fitur registrasi dan login untuk pengguna
✅ Pengelolaan data produk (menampilkan dan menambahkan data)
✅ Pengamanan endpoint menggunakan middleware auth:sanctum
✅ Pembatasan akses berdasarkan ability token seperti product-list dan product-store
✅ Implementasi role management menggunakan tabel roles dan user_role
✅ Token yang dihasilkan saat login secara otomatis membawa hak akses (ability) sesuai dengan role masing-masing user
