var express = require("express");
var router = express.Router();

/* GET home page. */
router.get("/", function(req, res, next) {
  res.render("index.html");
});
router.get("/register", function(req, res, next) {
  res.render("register.html");
});
router.get("/users/:id", function(req, res, next) {
  res.render("User.html");
});
router.get("/users/:id/exercise", function(req, res, next) {
  res.render("exercise.html");
});

module.exports = router;
