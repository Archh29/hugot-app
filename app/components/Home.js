import React, { useState, useEffect } from 'react';
import Post from './Post';

const Home = ({ userId, onLogout }) => {
  const [posts, setPosts] = useState([]);
  const [postText, setPostText] = useState('');

  useEffect(() => {
    if (userId) {
      fetchPosts();
    }
  }, [userId]);

  const fetchPosts = () => {
    fetch('http://localhost/hugot/api.php')
      .then(response => response.json())
      .then(data => setPosts(data))
      .catch(error => console.error('Error fetching posts:', error));
  };

  const addPost = async () => {
    try {
      const response = await fetch('http://localhost/hugot/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'addPost',
          user_id: userId,
          content: postText
        })
      });

      const result = await response.json();
      if (result.success) {
        setPosts([{
          post_id: result.post_id,
          user_id: result.user_id,
          first_name: result.first_name,
          last_name: result.last_name,
          content: postText,
          created_at: new Date().toISOString(),
          comments: []
        }, ...posts]);
        setPostText('');
      } else {
        console.error("Failed to add post:", result.message);
      }
    } catch (error) {
      console.error("An error occurred:", error);
    }
  };

  const addComment = (postId, commentText) => {
    fetch('http://localhost/hugot/api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'addComment',
        post_id: postId,
        user_id: userId,
        comment_text: commentText
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        setPosts(posts.map(post => 
          post.post_id === postId 
            ? {
                ...post,
                comments: [
                  ...post.comments,
                  {
                    comment_id: data.comment_id,
                    comment_text: commentText,
                    created_at: new Date().toISOString(),
                    comment_first_name: data.comment_first_name,
                    comment_last_name: data.comment_last_name
                  }
                ]
              }
            : post
        ));
      } else {
        console.error('Failed to add comment:', data.message);
      }
    });
  };

  const handleLogout = () => {
    onLogout();
  };

  return (
    <div className="relative">
      {/* Header */}
      <header className="w-full fixed top-0 left-0 flex justify-between items-center p-4 border-b border-gray-300 bg-gray-100 shadow-md z-50">
        <div className="text-xl font-bold">HugotBook</div>
        <button onClick={handleLogout} className="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition">
          Logout
        </button>
      </header>
      
      {/* Main Content */}
      <div className="pt-16 max-w-xl mx-auto mt-10">
        {/* Post Input */}
        <div className="mb-6">
          <textarea
            className="w-full p-2 border border-gray-300 rounded-lg"
            rows="3"
            placeholder="What's on your mind?"
            value={postText}
            onChange={(e) => setPostText(e.target.value)}
          />
          <button
            onClick={addPost}
            className="mt-2 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition w-full"
          >
            Post
          </button>
        </div>
        
        {/* Posts List */}
        <div>
          {posts.map(post => (
            <Post key={post.post_id} post={post} addComment={addComment} />
          ))}
        </div>
      </div>
    </div>
  );
};

export default Home;
  