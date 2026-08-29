class Kehadiran {
  final int id;
  final int hakimId;
  final String tanggal;
  final String status;
  final String namaHakim;
  final String nipHakim;

  Kehadiran({
    required this.id,
    required this.hakimId,
    required this.tanggal,
    required this.status,
    required this.namaHakim,
    required this.nipHakim,
  });

  factory Kehadiran.fromJson(Map<String, dynamic> json) {
    return Kehadiran(
      id: json['id'],
      hakimId: json['hakim_id'],
      tanggal: json['tanggal'],
      status: json['status'],
      namaHakim: json['hakim']?['nama'] ?? 'Tidak Diketahui',
      nipHakim: json['hakim']?['nip'] ?? '-',
    );
  }
}
