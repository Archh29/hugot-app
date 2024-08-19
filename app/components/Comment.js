export default function Comment({ comment }) {
  return (
    <div className="border-t pt-2 mt-2">
      <p className="font-bold">{comment.user_id}</p> {/* Comment user */}
      <p>{comment.comment_text}</p> {/* Comment text */}
    </div>
  );
}
