<x-profile :sharedProfileData="$sharedProfileData" doctitle="Who {{$sharedProfileData['username']}} follows">

 @include("profile-following-only")

</x-profile>
