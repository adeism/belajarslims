Ketika coba Plugin baru eh malah error SLiMSnya. Bagaimana perbaikannnya ? 

1. **Identifikasi Plugin Error**: Cari folder plugin yang bermasalah di direktori `plugins`.

2. **Hapus/Ganti Nama Folder**: Ubah nama folder plugin, misalnya `plugin-error` menjadi `plugin-error-disabled`.

3. **Hapus Entry di Database (Opsional)**:
   - Akses database SLiMS.
   - Hapus data plugin yang error di phpmyadmin atau mysql viewer lainnya
   - **Backup dulu database!**

4. **Uji Coba**: Reload SLiMS dan pastikan error hilang.

*jika tidak berhasil mungkin plugin yang Anda coba mengubah database/sistem yang lebih kompleks
