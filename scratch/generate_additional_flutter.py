import os

base_path = r"c:\Aplikasi Pengadilan\flutter_source"
os.makedirs(os.path.join(base_path, "lib/models"), exist_ok=True)
os.makedirs(os.path.join(base_path, "lib/providers"), exist_ok=True)
os.makedirs(os.path.join(base_path, "lib/screens"), exist_ok=True)

files = {
    "lib/models/kehadiran_model.dart": """class Kehadiran {
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
""",
    "lib/providers/kehadiran_provider.dart": """import 'dart:convert';
import 'package:flutter/material.dart';
import '../core/api_client.dart';
import '../models/kehadiran_model.dart';

class KehadiranProvider with ChangeNotifier {
  List<Kehadiran> _listKehadiran = [];
  bool _isLoading = false;
  String _error = '';

  List<Kehadiran> get listKehadiran => _listKehadiran;
  bool get isLoading => _isLoading;
  String get error => _error;

  Future<void> fetchKehadiran() async {
    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final response = await ApiClient.get('/kehadiran');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'] as List;
        _listKehadiran = data.map((item) => Kehadiran.fromJson(item)).toList();
      } else {
        _error = 'Gagal mengambil data kehadiran';
      }
    } catch (e) {
      _error = 'Koneksi bermasalah: ${e.toString()}';
    }

    _isLoading = false;
    notifyListeners();
  }
}
""",
    "lib/providers/user_provider.dart": """import 'dart:convert';
import 'package:flutter/material.dart';
import '../core/api_client.dart';
import '../models/user_model.dart';

class UserProvider with ChangeNotifier {
  List<User> _users = [];
  bool _isLoading = false;
  String _error = '';

  List<User> get users => _users;
  bool get isLoading => _isLoading;
  String get error => _error;

  Future<void> fetchUsers() async {
    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final response = await ApiClient.get('/users');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'] as List;
        _users = data.map((item) => User.fromJson(item)).toList();
      } else {
        _error = 'Gagal mengambil data user';
      }
    } catch (e) {
      _error = 'Koneksi bermasalah: ${e.toString()}';
    }

    _isLoading = false;
    notifyListeners();
  }
}
""",
    "lib/screens/kehadiran_screen.dart": """import 'package:flutter/material.dart';
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
                  subtitle: 'NIP: ${item.nipHakim}\\nTanggal: ${item.tanggal}',
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
""",
    "lib/screens/user_list_screen.dart": """import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/user_provider.dart';
import '../widgets/custom_card.dart';

class UserListScreen extends StatefulWidget {
  const UserListScreen({Key? key}) : super(key: key);

  @override
  State<UserListScreen> createState() => _UserListScreenState();
}

class _UserListScreenState extends State<UserListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UserProvider>(context, listen: false).fetchUsers();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('User Management')),
      body: Consumer<UserProvider>(
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
                      onPressed: () => provider.fetchUsers(),
                      child: const Text('Coba Lagi'),
                    )
                  ],
                ),
              ),
            );
          }

          if (provider.users.isEmpty) {
            return const Center(child: Text('Tidak ada data user.'));
          }

          return RefreshIndicator(
            onRefresh: provider.fetchUsers,
            child: ListView.builder(
              itemCount: provider.users.length,
              itemBuilder: (context, index) {
                final user = provider.users[index];
                return CustomCard(
                  title: user.name,
                  subtitle: 'Email: ${user.email}',
                  trailing: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.purple.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      user.role.toUpperCase(),
                      style: const TextStyle(color: Colors.purple, fontWeight: FontWeight.bold, fontSize: 12),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
"""
}

for filepath, content in files.items():
    full_path = os.path.join(base_path, filepath)
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content)

print("Additional Flutter files successfully generated.")
