import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/jadwal_provider.dart';
import '../widgets/custom_card.dart';
import '../widgets/status_badge.dart';

class JadwalSidangScreen extends StatefulWidget {
  const JadwalSidangScreen({Key? key}) : super(key: key);

  @override
  State<JadwalSidangScreen> createState() => _JadwalSidangScreenState();
}

class _JadwalSidangScreenState extends State<JadwalSidangScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<JadwalProvider>(context, listen: false).fetchJadwal();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Jadwal Sidang Hari Ini')),
      body: Consumer<JadwalProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }
          
          if (provider.error.isNotEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.wifi_off, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text(provider.error),
                  ElevatedButton(onPressed: () => provider.fetchJadwal(), child: const Text('Coba Lagi'))
                ],
              ),
            );
          }

          if (provider.jadwals.isEmpty) {
            return const Center(child: Text('Tidak ada jadwal sidang hari ini.'));
          }

          return RefreshIndicator(
            onRefresh: provider.fetchJadwal,
            child: ListView.builder(
              itemCount: provider.jadwals.length,
              itemBuilder: (context, index) {
                final jadwal = provider.jadwals[index];
                return CustomCard(
                  title: 'No. ${jadwal.nomorPerkara}',
                  subtitle: 'Hakim: ${jadwal.namaHakim ?? "-"}\nRuang: ${jadwal.namaRuang ?? "-"}\nPukul: ${jadwal.waktuMulai}',
                  trailing: StatusBadge(status: jadwal.status),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
