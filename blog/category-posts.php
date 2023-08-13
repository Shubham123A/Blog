<?php 
include 'partials/header.php';
// fetch posts if id is set
if (isset($_GET['id'])) {
	$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
	$query = "SELECT * FROM posts WHERE category_id=$id ORDER BY date_time DESC";
	$posts = mysqli_query($connection, $query);
} else {
	header('location: ' . ROOT_URL . 'blog.php');
	die();
}
?>


    <header class="category_title">
        <h2>
            <?php
            //fetch category from categories table using category_id of post
            $category_query = "SELECT * FROM categories WHERE id=$id";
            $category_result = mysqli_query($connection, $category_query);
            $category = mysqli_fetch_assoc($category_result); 
            echo $category['title'];
            ?>
        </h2>
    </header>
    <!--=====================END OF CATEGORY TITLE============================-->
<?php if (mysqli_num_rows($posts) > 0) : ?>
    <section class="posts">
        <div class="container posts_container">
            <?php while($post= mysqli_fetch_assoc($posts)) : ?>
            <article class="posts">
                <div class="post_thumbnail" style="width: 300px; height: 200px;">
                    <img src="./images/<?= $post['thumbnail'] ?>">
                </div>
                <div class="post_info">
                    
                <h2 class="post_title"><a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h2>
						<a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>">

							<p class="post_body" style="min-height: 80px;">
								<?= substr($post['body'], 0, 120) ?>...
							</p> 

                        </a>
                    <div class="post_author">
                        <?php 
                        //fetch author from users table using author_id
                        $author_id = $post['author_id'];
                        $author_query = "SELECT * FROM users WHERE id=$author_id";
                        $author_result = mysqli_query($connection, $author_query);
                        $author = mysqli_fetch_assoc($author_result);
                        $author_firstname = $author['firstname'];
                        $author_lastname = $author['lastname'];
                        ?>
                        <div class="post_author-avatar">
                            <img src="./images/<?= $author['avatar'] ?>">
                        </div>

                        <div class="post_author-info">
                        <h5>By:  <?= "{$author_firstname} {$author_lastname}" ?></h5>
                            <small><?= date("M d, Y - H:i", strtotime($post['date_time'])) ?></small>
                        </div>    
                    </div>    
                </div>    
            </article>
            <?php endwhile ?>
        </div>        
    </section> 
    <?php else : ?>
	<div class="alert_message error lg" style="margin-top: 20px;">
		<p>No Posts found for this category</p>
	</div>
<?php endif; ?>    

<!--=====================================END OF POSTS================================================-->

<section class="category_button">
    <div class="container category_buttons-container">
        <?php
		$all_categories_query = "SELECT * FROM categories";
		$all_categories = mysqli_query($connection, $all_categories_query);
		?>
		<?php while ($category = mysqli_fetch_assoc($all_categories)) : ?>
			<a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>" class="category_button"><?= $category['title'] ?></a>
		<?php endwhile; ?>
    </div>    
</section>

 <!--=====================================END OF CATEGORY BUTTON================================================-->


<?php 
include 'partials/footer.php';
?>