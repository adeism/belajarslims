![image](https://github.com/user-attachments/assets/52532814-f4df-4ecc-af1d-14daef50bec9)


Untuk memisahkan nomor telepon dari alamat di template membercard "old" pada SLiMS 9 Bulian, Anda perlu memodifikasi file `files/membercard/old/membercard.php`. Berikut adalah langkah-langkahnya:

**1. Identifikasi Kode yang Menampilkan Alamat dan Nomor Telepon**

Cari bagian kode yang menampilkan alamat dan nomor telepon anggota.  Kemungkinan besar, kode ini akan terlihat seperti ini:

```php
        <p class="bio_address">
                <label class="bio_label">
                <?php echo __('Address') ?> /
                <?php echo __('Phone Number') ?>
                </label>
                <span style="float:left">: </span> 
                <?php echo $sysconf['print']['membercard']['include_address_label']?'':'-->' ?>
                <?php echo $sysconf['print']['membercard']['include_address_label']?'':'<!--' ?>
                <span class="label_address">
                  <?php echo $card['member_address'] ?> /
                  <?php echo $card['member_phone'] ?>
                </span>
              </p>
```

**2. Memisahkan Kode**

Ubah kode di atas menjadi dua bagian terpisah: satu untuk menampilkan alamat dan satu lagi untuk menampilkan nomor telepon.  Berikut adalah contoh kode yang telah dimodifikasi:

```php
      <p class="bio_address">
          <label class="bio_label"><?php echo __('Address') ?></label>
          <span style="float:left">: </span>
          <?php echo $sysconf['print']['membercard']['include_address_label']?'':'-->' ?>
          <?php echo $sysconf['print']['membercard']['include_address_label']?'':'<!--' ?>
          <span class="label_address"><?php echo $card['member_address'] ?></span>
      </p>
      <p class="bio_address">
          <label class="bio_label"><?php echo __('Phone Number') ?></label>
          <span style="float:left">: </span>
          <span class="label_address"><?php echo $card['member_phone'] ?></span>
      </p>
```

**Penjelasan Perubahan**

*   Kode awal digandakan.
*   Label untuk setiap bagian diubah agar sesuai (Alamat dan Nomor Telepon).
*   `.bio_address` tetap digunakan karena untuk gaya yang sudah ada.

**3. Simpan Perubahan**

Simpan file `files/membercard/old/membercard.php` setelah Anda selesai memodifikasi kode.

**Penting:**

*   Pastikan kode yang Anda masukkan benar dan tidak merusak struktur template.
*   Pastikan Anda sudah melakukan backup file template.

Dengan mengikuti langkah-langkah di atas, nomor telepon dan alamat akan ditampilkan dalam baris terpisah pada kartu anggota.
