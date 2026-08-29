import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/kehadiran_provider.dart';
import '../widgets/custom_card.dart';
import '../widgets/status_badge.dart';

class KehadiranScreen extends StatefulWidget {
  const KehadiranScreen({Key? key}) : super(key: key);

  @override
  State<KehadiranScreen> createState() => _KehadiranScreenState();
}

class _KehadiranScreenState extends State<KehadiranScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<KehadiranProvider>(context, listen: false).fetchKehadiran();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Kehadiran Hakim')),
      body: Consumer<KehadiranProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.error.isNotEmpty) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.wifi_off, size: 64, color: Colors.redAccent),
                    const SizedBox(height: 16),
                    Text(provider.error, textAlign: TextAlign.center),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () => provider.fetchKehadiran(),
                      child: const Text('Coba Lagi'),
                    )
                  ],
                ),
              ),
            );
          }

          if (provider.listKehadiran.isEmpty) {
            return const Center(child: Text('Tidak ada data kehadiran hari ini.'));
          }

          return RefreshIndicator(
            onRefresh: provider.fetchKehadiran,
            child: ListView.builder(
              itemCount: provider.listKehadiran.length,
              itemBuilder: (context, index) {
                final item = provider.listKehadiran[index];
                return CustomCard(
                  title: item.namaHakim,
                  subtitle: 'NIP: ${item.nipHakim}\nTanggal: ${item.tanggal}',
                  trailing: StatusBadge(status: item.status),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
