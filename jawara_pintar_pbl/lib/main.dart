import 'package:flutter/material.dart';
import 'package:jawara_pintar_pbl/constants/app_routes.dart';
import 'package:jawara_pintar_pbl/providers/auth_provider.dart';
import 'package:jawara_pintar_pbl/providers/rw_provider.dart';
import 'package:jawara_pintar_pbl/screens/auth/login_screen.dart';
import 'package:jawara_pintar_pbl/screens/home/home_screen.dart';
import 'package:jawara_pintar_pbl/screens/splash_screen.dart';
import 'package:provider/provider.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (ctx) => AuthProvider()),

        ChangeNotifierProxyProvider<AuthProvider, RwProvider>(
          create: (ctx) => RwProvider(null, []),
          update: (ctx, auth, previousRwProvider) => RwProvider(
            auth.token,
            previousRwProvider == null ? [] : previousRwProvider.items,
          ),
        ),
      ],
      child: Consumer<AuthProvider>(
        builder: (ctx, auth, _) => MaterialApp(
          title: 'Aplikasi Rukun',
          debugShowCheckedModeBanner: false,
          theme: ThemeData(
            primarySwatch: Colors.green,
            scaffoldBackgroundColor: Colors.grey[100],
            appBarTheme: const AppBarTheme(
              backgroundColor: Colors.white,
              elevation: 0,
              iconTheme: IconThemeData(color: Colors.black87),
              titleTextStyle: TextStyle(
                color: Colors.black87,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),

          home: auth.isAuth
              ? const HomeScreen()
              : FutureBuilder(
                  future: auth.tryAutoLogin(),
                  builder: (ctx, authResultSnapshot) =>
                      authResultSnapshot.connectionState ==
                          ConnectionState.waiting
                      ? const SplashScreen()
                      : const LoginScreen(),
                ),
          routes: {
            AppRoutes.login: (ctx) => const LoginScreen(),
            AppRoutes.home: (ctx) => const HomeScreen(),
          },
        ),
      ),
    );
  }
}
