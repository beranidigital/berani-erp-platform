<?php

return [
    'title'                   => 'Profil',
    'heading'                 => 'Profil',
    'subheading'              => 'Kelola pengaturan akun dan preferensi Anda.',
    'information_section'     => 'Informasi Profil',
    'information_description' => 'Perbarui informasi profil dan alamat email akun Anda.',

    'notification' => [
        'success' => [
            'title' => 'Profil Diperbarui',
            'body'  => 'Profil Anda berhasil diperbarui.',
        ],

        'error' => [
            'title' => 'Gagal Memperbarui Profil',
            'body'  => 'Terjadi kesalahan saat memperbarui profil.',
        ],
    ],

    'actions' => [
        'save' => 'Simpan Perubahan',
    ],

    'fields' => [
        'avatar'   => 'Foto Profil',
        'name'     => 'Nama',
        'email'    => 'Email',
        'language' => 'Bahasa Preferensi',
    ],

    'password' => [
        'section'     => 'Perbarui Kata Sandi',
        'description' => 'Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.',
        'current'     => 'Kata Sandi Saat Ini',
        'new'         => 'Kata Sandi Baru',
        'confirm'     => 'Konfirmasi Kata Sandi',
        'helper'      => 'Minimal 8 karakter.',

        'notification' => [
            'success' => [
                'title' => 'Kata Sandi Diperbarui',
                'body'  => 'Kata sandi Anda berhasil diperbarui.',
            ],

            'error' => [
                'title' => 'Gagal Memperbarui Kata Sandi',
                'body'  => 'Terjadi kesalahan saat memperbarui kata sandi.',
            ],
        ],
    ],
];
