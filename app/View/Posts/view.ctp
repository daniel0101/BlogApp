<h1><?php echo h($post['Post']['title']); ?></h1>

<p><b>Author:</b> <?php echo $post['User']['username'] ?>   <b>Created:</b> <?php echo $post['Post']['created']; ?></p>

<p><?php echo h($post['Post']['body']); ?></p>