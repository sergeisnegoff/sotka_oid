<?php
// Home
Breadcrumbs::for('product', function ($trail) {
    $trail->push('Home', route('home'));
});

// Home > About
Breadcrumbs::for('about', function ($trail) {
    $trail->parent('product');
    $trail->push('About', route('about'));
});

// Home > Blog
Breadcrumbs::for('blog', function ($trail) {
    $trail->parent('product');
    $trail->push('Blog', route('blog'));
});

// Home > Blog > [Category]
Breadcrumbs::for('category', function ($trail, $category) {
    $trail->parent('blog');
    $trail->push($category->title, route('category', $category->id));
});

// Home > Blog > [Category] > [Post]
Breadcrumbs::for('product', function ($trail, $product) {
    $trail->parent('category', $product->category);
    $trail->push($product->title, route('product', $product->id));
});
