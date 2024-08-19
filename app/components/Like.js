import { useState } from 'react';

export default function LikeButton({ post }) {
  const [likes, setLikes] = useState(post.likes);

  const handleLike = () => {
    setLikes(likes + 1);
  };

  return (
    <button
      onClick={handleLike}
      className="mt-2 bg-red-500 text-white p-2 rounded"
    >
      Like ({likes})
    </button>
  );
}
