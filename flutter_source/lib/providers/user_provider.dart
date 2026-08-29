import 'dart:convert';
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
