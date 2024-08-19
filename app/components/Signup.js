'use client';
import React, { useState } from 'react';

const Signup = ({ onSignupSuccess, onClose }) => {
  const [formData, setFormData] = useState({
    firstName: '',
    middleName: '',
    lastName: '',
    username: '',
    password: '',
    confirmPassword: '',
    email: '',
    phone: '',
    address: ''
  });
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSignup = async () => {
    if (formData.password !== formData.confirmPassword) {
      setError('Passwords do not match.');
      return;
    }

    try {
      const response = await fetch('http://localhost/hugot/signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });
      const data = await response.json();

      if (data.success) {
        setSuccess('Signup successful! You can now log in.');
        setError('');
        onSignupSuccess();
      } else {
        setError(data.message);
      }
    } catch (error) {
      setError('An error occurred. Please try again.');
    }
  };

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
      <div className="bg-white p-6 rounded-lg max-w-md w-full relative">
        <button
          onClick={onClose} // Ensure onClose is defined and passed as a prop
          className="absolute top-2 right-2 p-2 text-gray-500 hover:text-gray-700 z-10"
          aria-label="Close"
        >
          <svg xmlns="http://www.w3.org/2000/svg" className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <h1 className="text-xl font-bold mb-4">Sign Up</h1>
        {error && <p className="text-red-500 mb-4">{error}</p>}
        {success && <p className="text-green-500 mb-4">{success}</p>}
        
        <input
          type="text"
          name="firstName"
          className="w-full p-2 mb-4 border rounded"
          placeholder="First Name"
          value={formData.firstName}
          onChange={handleChange}
        />
        <input
          type="text"
          name="middleName"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Middle Name"
          value={formData.middleName}
          onChange={handleChange}
        />
        <input
          type="text"
          name="lastName"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Last Name"
          value={formData.lastName}
          onChange={handleChange}
        />
        <input
          type="text"
          name="username"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Username"
          value={formData.username}
          onChange={handleChange}
        />
        <input
          type="password"
          name="password"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Password"
          value={formData.password}
          onChange={handleChange}
        />
        <input
          type="password"
          name="confirmPassword"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Confirm Password"
          value={formData.confirmPassword}
          onChange={handleChange}
        />
        <input
          type="email"
          name="email"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Email"
          value={formData.email}
          onChange={handleChange}
        />
        <input
          type="tel"
          name="phone"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Phone Number"
          value={formData.phone}
          onChange={handleChange}
        />
        <input
          type="text"
          name="address"
          className="w-full p-2 mb-4 border rounded"
          placeholder="Address"
          value={formData.address}
          onChange={handleChange}
        />
        
        <button
          onClick={handleSignup}
          className="w-full bg-blue-500 text-white p-2 rounded"
        >
          Sign Up
        </button>
      </div>
    </div>
  );
};

export default Signup;
