import os

base_path = r"c:\Aplikasi Pengadilan\flutter_source"
folders = [
    "lib/core",
    "lib/models",
    "lib/providers",
    "lib/screens",
    "lib/widgets",
]

for folder in folders:
    os.makedirs(os.path.join(base_path, folder), exist_ok=True)

files = {
    "pubspec.yaml": """name: sipp_mobile
description: Aplikasi Mobile SIPP & Judicial Intelligence App
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: '>=3.0.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0
  provider: ^6.0.5
  flutter_secure_storage: ^8.0.0
  shared_preferences: ^2.2.1
  intl: ^0.18.1

flutter:
  uses-material-design: true
""",
    "lib/main.dart": """import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/theme.dart';
import 'providers/auth_provider.dart';
import 'providers/jadwal_provider.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => JadwalProvider()),
      ],
      child: const SIPPApp(),
    ),
  );
}

class SIPPApp extends StatelessWidget {
  const SIPPApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SIPP Mobile',
      theme: AppTheme.lightTheme,
      home: Consumer<AuthProvider>(
        builder: (context, auth, _) {
          if (auth.isLoading) {
            return const Scaffold(body: Center(child: CircularProgressIndicator()));
          }
          return auth.isAuthenticated ? const DashboardScreen() : const LoginScreen();
        },
      ),
    );
  }
}
""",
    "lib/core/constants.dart": """class AppConstants {
  // Ganti dengan IP lokal Anda jika menggunakan emulator, misal: http://10.0.2.2:8000/api
  static const String baseUrl = 'http://10.0.2.2:8000/api'; 
}
""",
    "lib/core/api_client.dart": """import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'constants.dart';

class ApiClient {
  static const storage = FlutterSecureStorage();

  static Future<Map<String, String>> _getHeaders() async {
    String? token = await storage.read(key: 'auth_token');
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<http.Response> get(String endpoint) async {
    try {
      final headers = await _getHeaders();
      final response = await http.get(Uri.parse('${AppConstants.baseUrl}$endpoint'), headers: headers);
      return response;
    } on SocketException {
      throw Exception('Tidak ada koneksi internet');
    }
  }

  static Future<http.Response> post(String endpoint, Map<String, dynamic> body) async {
    try {
      final headers = await _getHeaders();
      final response = await http.post(
        Uri.parse('${AppConstants.baseUrl}$endpoint'),
        headers: headers,
        body: jsonEncode(body),
      );
      return response;
    } on SocketException {
      throw Exception('Tidak ada koneksi internet');
    }
  }
}
""",
    "lib/core/theme.dart": """import 'package:flutter/material.dart';

class AppTheme {
  static final ThemeData lightTheme = ThemeData(
    primarySwatch: Colors.indigo,
    scaffoldBackgroundColor: Colors.grey[100],
    appBarTheme: const AppBarTheme(
      elevation: 0,
      centerTitle: true,
      backgroundColor: Colors.indigo,
      foregroundColor: Colors.white,
    ),
    cardTheme: CardTheme(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
    ),
  );
}
""",
    "lib/models/user_model.dart": """class User {
  final int id;
  final String name;
  final String email;
  final String role;

  User({required this.id, required this.name, required this.email, required this.role});

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'] ?? 'masyarakat',
    );
  }
}
""",
    "lib/models/jadwal_sidang_model.dart": """class JadwalSidang {
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
""",
    "lib/providers/auth_provider.dart": """import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../core/api_client.dart';
import '../models/user_model.dart';

class AuthProvider with ChangeNotifier {
  User? _user;
  bool _isAuthenticated = false;
  bool _isLoading = true;
  final _storage = const FlutterSecureStorage();

  User? get user => _user;
  bool get isAuthenticated => _isAuthenticated;
  bool get isLoading => _isLoading;

  AuthProvider() {
    _checkToken();
  }

  Future<void> _checkToken() async {
    String? token = await _storage.read(key: 'auth_token');
    if (token != null) {
      try {
        final response = await ApiClient.get('/me');
        if (response.statusCode == 200) {
          _user = User.fromJson(jsonDecode(response.body)['user']);
          _isAuthenticated = true;
        } else {
          await logout();
        }
      } catch (e) {
        _isAuthenticated = true; // Assume authenticated if offline, or handle properly
      }
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    try {
      final response = await ApiClient.post('/login', {'email': email, 'password': password});
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        await _storage.write(key: 'auth_token', value: data['access_token']);
        _user = User.fromJson(data['user']);
        _isAuthenticated = true;
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<void> logout() async {
    await ApiClient.post('/logout', {});
    await _storage.delete(key: 'auth_token');
    _user = null;
    _isAuthenticated = false;
    notifyListeners();
  }
}
""",
    "lib/providers/jadwal_provider.dart": """import 'dart:convert';
import 'package:flutter/material.dart';
import '../core/api_client.dart';
import '../models/jadwal_sidang_model.dart';

class JadwalProvider with ChangeNotifier {
  List<JadwalSidang> _jadwals = [];
  bool _isLoading = false;
  String _error = '';

  List<JadwalSidang> get jadwals => _jadwals;
  bool get isLoading => _isLoading;
  String get error => _error;

  Future<void> fetchJadwal() async {
    _isLoading = true;
    _error = '';
    notifyListeners();

    try {
      final response = await ApiClient.get('/jadwal-sidang');
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'] as List;
        _jadwals = data.map((j) => JadwalSidang.fromJson(j)).toList();
      } else {
        _error = 'Gagal memuat jadwal';
      }
    } catch (e) {
      _error = e.toString();
    }

    _isLoading = false;
    notifyListeners();
  }
}
""",
    "lib/widgets/status_badge.dart": """import 'package:flutter/material.dart';

class StatusBadge extends StatelessWidget {
  final String status;
  const StatusBadge({Key? key, required this.status}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    Color bgColor;
    Color textColor = Colors.white;

    switch (status.toUpperCase()) {
      case 'TERJADWAL':
        bgColor = Colors.blue;
        break;
      case 'PUTUS':
      case 'SELESAI':
        bgColor = Colors.green;
        break;
      case 'PROSES':
        bgColor = Colors.orange;
        break;
      default:
        bgColor = Colors.grey;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(12)),
      child: Text(
        status,
        style: TextStyle(color: textColor, fontSize: 12, fontWeight: FontWeight.bold),
      ),
    );
  }
}
""",
    "lib/widgets/custom_card.dart": """import 'package:flutter/material.dart';

class CustomCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final Widget trailing;
  final VoidCallback? onTap;

  const CustomCard({
    Key? key,
    required this.title,
    required this.subtitle,
    required this.trailing,
    this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 8),
                    Text(subtitle, style: TextStyle(color: Colors.grey[700], fontSize: 14)),
                  ],
                ),
              ),
              trailing,
            ],
          ),
        ),
      ),
    );
  }
}
""",
    "lib/screens/login_screen.dart": """import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _loading = false;

  void _login() async {
    setState(() => _loading = true);
    final auth = Provider.of<AuthProvider>(context, listen: false);
    bool success = await auth.login(_emailCtrl.text, _passCtrl.text);
    setState(() => _loading = false);
    
    if (!success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Login Gagal')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Card(
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text('SIPP Mobile', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.indigo)),
                  const SizedBox(height: 24),
                  TextField(controller: _emailCtrl, decoration: const InputDecoration(labelText: 'Email', border: OutlineInputBorder())),
                  const SizedBox(height: 16),
                  TextField(controller: _passCtrl, obscureText: true, decoration: const InputDecoration(labelText: 'Password', border: OutlineInputBorder())),
                  const SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 16)),
                      onPressed: _loading ? null : _login,
                      child: _loading ? const CircularProgressIndicator(color: Colors.white) : const Text('LOGIN'),
                    ),
                  )
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
""",
    "lib/screens/dashboard_screen.dart": """import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'jadwal_sidang_screen.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => Provider.of<AuthProvider>(context, listen: false).logout(),
          )
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Selamat Datang, ${user?.name}', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          Text('Role: ${user?.role.toUpperCase()}', style: const TextStyle(color: Colors.grey)),
          const SizedBox(height: 24),
          
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            children: [
              _buildMenuCard(context, 'Jadwal Sidang', Icons.calendar_today, Colors.blue, const JadwalSidangScreen()),
              _buildMenuCard(context, 'Perkara', Icons.folder, Colors.orange, null), // Implement future screens
              _buildMenuCard(context, 'Antrean', Icons.people, Colors.green, null),
              _buildMenuCard(context, 'Laporan', Icons.pie_chart, Colors.purple, null),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildMenuCard(BuildContext context, String title, IconData icon, Color color, Widget? target) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: InkWell(
        onTap: () {
          if (target != null) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => target));
          } else {
            ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Segera Hadir')));
          }
        },
        borderRadius: BorderRadius.circular(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircleAvatar(radius: 30, backgroundColor: color.withOpacity(0.1), child: Icon(icon, size: 30, color: color)),
            const SizedBox(height: 12),
            Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }
}
""",
    "lib/screens/jadwal_sidang_screen.dart": """import 'package:flutter/material.dart';
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
                  subtitle: 'Hakim: ${jadwal.namaHakim ?? "-"}\\nRuang: ${jadwal.namaRuang ?? "-"}\\nPukul: ${jadwal.waktuMulai}',
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
"""
}

for filepath, content in files.items():
    full_path = os.path.join(base_path, filepath)
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content)

print("Flutter source code generated successfully.")
