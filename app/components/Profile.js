import React, { useEffect, useState } from 'react';

const Profile = ({ userId }) => {
  const [profile, setProfile] = useState(null);

  useEffect(() => {
    fetch('http://localhost/hugot/getProfile.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ user_id: userId })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          setProfile(data.profile);
        } else {
          console.error('Failed to fetch profile:', data.message);
        }
      })
      .catch(error => console.error('Error fetching profile:', error));
  }, [userId]);

  if (!profile) return <div>Loading profile...</div>;

  return (
    <div className="border rounded-lg p-4">
      <img src={profile.profile_picture} alt="Profile" className="w-16 h-16 rounded-full" />
      <h3 className="text-lg font-bold">{profile.username}</h3>
      <p>{profile.location}</p>
      <p>Joined: {new Date(profile.created_at).toLocaleDateString()}</p>
    </div>
  );
};

export default Profile;
