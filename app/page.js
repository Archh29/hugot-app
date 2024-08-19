'use client';
import React, { useState, useEffect } from 'react';
import Home from './components/Home';
import Login from './components/Login';
import Signup from './components/Signup';

const MainPage = () => {
  const [userId, setUserId] = useState(null);
  const [view, setView] = useState('login'); // 'login', 'signup', or 'home'

  useEffect(() => {
    // You can fetch user information from your server here if needed
  }, []);

  const handleLogin = (id) => {
    setUserId(id);
    setView('home'); // Change view to home after successful login
  };

  const handleSignup = () => {
    setView('signup');
  };

  const handleSignupSuccess = () => {
    setView('login');
  };

  const handleLogout = () => {
    setUserId(null);
    setView('login'); // Reset view to login after logout
  };

  if (view === 'home') {
    return <Home userId={userId} onLogout={handleLogout} />;
  }

  return view === 'signup' ? (
    <Signup onSignupSuccess={handleSignupSuccess} />
  ) : (
    <Login onLogin={handleLogin} onSignup={handleSignup} />
  );
};

export default MainPage;
