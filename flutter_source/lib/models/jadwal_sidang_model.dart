class JadwalSidang {
  final int id;
  final String nomorPerkara;
  final String waktuMulai;
  final String status;
  final String? namaHakim;
  final String? namaRuang;

  JadwalSidang({
    required this.id,
    required this.nomorPerkara,
    required this.waktuMulai,
    required this.status,
    this.namaHakim,
    this.namaRuang,
  });

  factory JadwalSidang.fromJson(Map<String, dynamic> json) {
    return JadwalSidang(
      id: json['id'],
      nomorPerkara: json['nomor_perkara'],
      waktuMulai: json['waktu_mulai'],
      status: json['status'],
      namaHakim: json['hakim']?['nama'],
      namaRuang: json['ruang_sidang']?['nama_ruangan'],
    );
  }
}
