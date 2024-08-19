import React, { useState } from 'react';

const Post = ({ post, addComment }) => {
  const { post_id, first_name, last_name, content, comments, created_at } = post;
  const [commentText, setCommentText] = useState('');

  const handleAddComment = () => {
    if (commentText.trim()) {
      addComment(post_id, commentText);
      setCommentText('');
    }
  };

  return (
    <div className="mb-6 p-4 border border-gray-300 rounded-lg shadow-md bg-white">
      <div className="flex items-center mb-2">
        <img 
          src="https://via.placeholder.com/40" 
          alt={`${first_name} ${last_name}`} 
          className="w-10 h-10 rounded-full mr-3" 
        />
        <div className="font-bold text-lg">{`${first_name} ${last_name}`}</div>
      </div>
      <div className="mt-2 text-gray-800">{content}</div>
      <div className="mt-2 text-gray-500 text-sm">Posted on: {new Date(created_at).toLocaleDateString()}</div>
      <div className="mt-4">
        <textarea
          className="w-full p-2 border border-gray-300 rounded-lg"
          rows="2"
          placeholder="Add a comment..."
          value={commentText}
          onChange={(e) => setCommentText(e.target.value)}
        />
        <button
          onClick={handleAddComment}
          className="mt-2 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition"
        >
          Add Comment
        </button>
      </div>
      <div className="mt-4">
        {comments.map((comment) => (
          <div key={comment.comment_id} className="p-2 border-t border-gray-200">
            <div className="flex items-center mb-1">
              <img 
                src="https://via.placeholder.com/30" 
                alt={`${comment.comment_first_name} ${comment.comment_last_name}`} 
                className="w-8 h-8 rounded-full mr-2" 
              />
              <div className="font-semibold">
                {comment.comment_first_name} {comment.comment_last_name}
              </div>
            </div>
            <div>{comment.comment_text}</div>
            <div className="text-gray-400 text-xs">Commented on: {new Date(comment.created_at).toLocaleDateString()}</div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Post;
