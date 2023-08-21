<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-firestore.js"></script>
<script>
    // Initialize Firebase
    const firebaseConfig = {
    apiKey: "AIzaSyAmisyUtJ_RG0G-lyi8s1ufqL0xDjiULkg",
    authDomain: "absensi-sinarindo.firebaseapp.com",
    projectId: "absensi-sinarindo",
    storageBucket: "absensi-sinarindo.appspot.com",
    messagingSenderId: "242536110981",
    appId: "1:242536110981:web:1f246fe18e5f739aa9c2ec",
    measurementId: "G-J6DKKQ70BQ"
    };
    firebase.initializeApp(config);
    var facebookProvider = new firebase.auth.FacebookAuthProvider();
    var googleProvider = new firebase.auth.GoogleAuthProvider();
    var facebookCallbackLink = '/login/facebook/callback';
    var googleCallbackLink = '/login/google/callback';
    async function socialSignin(provider) {
        var socialProvider = null;
        if (provider == "facebook") {
            socialProvider = facebookProvider;
            document.getElementById('social-login-form').action = facebookCallbackLink;
        } else if (provider == "google") {
            socialProvider = googleProvider;
            document.getElementById('social-login-form').action = googleCallbackLink;
        } else {
            return;
        }
        firebase.auth().signInWithPopup(socialProvider).then(function(result) {
            result.user.getIdToken().then(function(result) {
                document.getElementById('social-login-tokenId').value = result;
                document.getElementById('social-login-form').submit();
            });
        }).catch(function(error) {
            // do error handling
            console.log(error);
        });
    }
</script>
