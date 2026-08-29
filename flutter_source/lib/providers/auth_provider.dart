import 'dart:convert';
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
