# Mattbox

![Mattbox](src/images/versions/v4.png)

The **Mattbox** was a custom shoutbox written in PHP designed to mimic the [Inferno Shoutbox](http://vbulletin-mods.com/forum/showthread.php?t=4741) vBulletin plugin.
Made in 2012, It was my first introduction to programming. 

This old code is horrifyingly hacky in almost every way and was riddled with security vulnerabilities (See [Post-Mortem](#post-mortem)).
A lot of blood, sweat, tears, and frustration went into creating this seemingly simple application long ago, and it has a special place in my heart.

The purpose of this repository is to archive the memories of my first programming project, and serve as a reminder of how much I have learned since.

## Motivation
Many years ago I was a member on a gaming forum that had a large community. They had a shoutbox that a lot of people used and it was heavily moderated.
As time went on, the mods started to ban users and severely limit conversation topics (especially criticism about the site).
Thier shoutbox was also secretly modified to allow the moderators to spy on private conversations between users.

The Mattbox was created out of a community desire for a similar shoutbox experience with a focus on freedom of speech and privacy, giving power back to the users.

Unfortunately, everyone who was interested in this new platform had no experience with programming or web development (including myself), so I had to learn a lot as I brought each part of it to life.

## Post-Mortem
There are too many ugly parts about this code to write about here, so you'll have to dive into the source code if you want to see it in all of its glory.
I will list some of the funniest or most absurd things that stand out to me here:

### 1. Hardcoding
So many things that should never be hard-coded are hard-coded into the program.
Most notably, all HTML output and some CSS is woven into the PHP source, which is naturally a nightmare to change. 
This is so pervasive, but here are a few small examples:

```php
if($nameExists == 0)
{
	die('<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> Your name has changed, or you have been signed out. Please <a href="logout.php">Log Out</a>.');
}
```
*Hardcoded CSS, HTML, mixed with logic.
([src](src/showshouts.php#L118-L121))*

*See also `style_username()`
([src](src/functions.php#L482-L528))*

### 2. Mixing all the things

![xd](https://i.imgur.com/Q5duq0R.jpg)

*Me working on the Mattbox, Circa 2012*

Any kind of page in my app was basically a `.php` file that was written as a handmade HTML page with CSS, JS, and PHP code sprinkled throughout a single file.

In fact, the HTML, PHP, and CSS are so deeply interwoven that redesigning the layout would be an excrutiatingly painful and time consuming process.
It would likely be faster to re-write the entire application.

There are so many functions littered with raw SQL queries that are also mixed with hardcoded HTML formatting and CSS styling.

Sometimes I had separate CSS stylesheets, and sometimes I wrote inline style tags.
I guess it depended on what mood I was in that day.

I regularly used `<?php` tags to populate data in `<style>` and `<script>` tags:

```html
<script type="text/javascript">
function setDefaults()
{
	optionColor = "<?php echo ucfirst($txtColor); ?>";
	optionFont = "<?php echo $txtFont; ?>";
	options = document.getElementsByTagName('option');
```
```html
<style type="text/css"><?php if(isset($display)) { ?>
body {
		background-image:url('<?php echo $display['Background']; ?>');
		<?php if ($display['Repeat'] == 0) { ?>
		background-size:100%;
		<?php } else { ?>
		background-repeat:repeat;
		<?php } ?>
}
```
*Echoing information directly into Javascript and CSS with PHP.
([src](src/index.php#L517-L526))*

### 3. Giant `functions.php` file
Since I tended to imagine that every `.php` file was an "HTML page with logic" that would eventually be spit out to the screen,
I bundled all the functions I thought I'd ever have to use into a massive `functions.php` file that I could include at the top of each page.
(How descriptive!)

The [`functions.php`](src/functions.php) file is nearly 900 lines long, and has functions that do everything from managing the session, 
to checking if certain smilies exist in the database, to formatting shouts according to user settings in a database. 

### 4. Reinventing the wheel
I tended to reinvent the wheel in so many areas that I had no business re-inventing.
I do remember wondering at the time if I should be re-writing some of these basic things,
but in my defense I was having so much fun learning how to do it myself to care.

I re-wrote functions that handled and [converted time](src/functions.php#L643-L684) (and mixed them with HTML),
made homemade functions that [parse URLs](src/functions.php#L735-L759) from messages,
[parsing BBCode](src/functions.php#L765-L789) from shouts, etc.

### 5. Security Vulnerabilities

Considering that I was learning fundamental programming concepts for the first time as I developed this project,
there were naturally a ton of security issues and bad design chioces that could have been (and were) exploited.

It should go without saying that I learned the hard way that I needed to sanitize database input.

Another notable example:

Early on, I stored the user's `username` in a cookie when they successfully logged into their account. 
I then used the `username` cookie as a unique identifier for that person, and used that string for validation everywhere in my code whenever someone completed an action.

One day, someone figured this out, and simply changed their `username` cookie to match my name, and like magic they instantly were logged into my account. From there, they deleted users, messed with things, and wreaked havoc on our little private community.

This led to me being confused, uninstalling and re-installing the software, wiping the database, and having everyone re-register. 

After thinking that my problem was solved, the guy did it again, and I was worried, confused, and I had to seriuosly audit my code to find out how it was happening. I eventually found this massive flaw.

### 6. What is source control?

As a beginning programmer, I had no idea what source control was. My version of source control was zipping up the source folder and putting the zip file somewhere else on my computer or backing it up with a USB drive.

I thought that I had lost this project until I stumbled across this snapshot of the project on an old flash drive.
Unfortunately I can't go back and look at the *very* beginning source, but this was built off of it and is close enough.

I did have an HTML changelog that I added to as I developed the Mattbox, which can be found [here](src/history.html).
Images of a few of the versions can be found [here](src/images/versions).

---

The current software hosted at the old mattbox.org domain is not related to this repository and is a newly re-imagined version of the Mattbox by [xijbx](https://github.com/xijbx) found [here](https://github.com/xijbx/mattbox).