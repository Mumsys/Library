**Warning**

CREATE BACKUPS OF YOUR FILES BEFORE YOU USE THIS TOOL!!!

BEFORE YOU START: CREATE BACKUPS OF YOUR FILES!!!



<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of contents**

+ [ShellTools](#shelltools)
  + [Requirements:](#requirements)
  + [Available adapters](#available-adapters)
    + [Adapters for exiftool](#adapters-for-exiftool)
      + [Action exiffixtimestamps](#action-exiffixtimestamps)
      + [Action exifmeta2filename](#action-exifmeta2filename)
      + [Action exiffilename2meta](#action-exiffilename2meta)
    + [Adapters for imagemagic:convert](#adapters-for-imagemagicconvert)
      + [Adapter resizeimages](#adapter-resizeimages)
    + [Adapters for ffmpeg](#adapters-for-ffmpeg)
      + [Action ffmpegcuttrimvideo](#action-ffmpegcuttrimvideo)
      + [Adapter resizevideos](#adapter-resizevideos)
    + [General hints for ffmpeg](#general-hints-for-ffmpeg)
    + [General hints for exiftool and in php context](#general-hints-for-exiftool-and-in-php-context)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->



# ShellTools 

`bin/shelltools.php`

This tool is a helper around linux command tools (e.g: `exiftool`, `ffmpeg`) or 
other tools i create for daily tasks or tasks i need. Now starting to collect 
things here more central.

`bin/shelltools.php --help` would shows you a lot how to use the actions/ 
options. Here in detail.

OS's independence will follow but not in yet. I dont have windows.

But: With the global `--test` flag you can run all commands, but you only get
output for the shell commands on a linux system but no real execution.

This program has several actions to solve common problems with media files. 
Photos or videos.

Currently: exif timestamp manipulation or video cutting or video and image resizing.

Because of the generic implementation, other tools will be integrated here also.

E.g: [multirename](https://github.com/flobee/multirename) which is based on this
repository will be bundeld different in the future to this project.



## Requirements:

php >= 8

Prefer: linux OS

Packages to run all adapters in real: exiftool, ffmpeg, imagemagick
(`apt install exiftool ffmpeg imagemagick`) (Base system: debian)



## Available adapters



### Adapters for exiftool

Exiftool is the tools around metadata in media files like videos or images/ 
photos:

exiftool can read metadata in media files and in some cases write/update them.

Especially for timestamps of photos from your camara (the metadata inside a 
photo) and the timstamps on the filesystem/ SD Card or the name of the file: 
This is sometimes very different:

Once you forget to set the time of your camara you may wonder about ages of
differences. On the filesystem or in the metadata or filename of each photo.

This adapters can solve some of this problems.


#### Action exiffixtimestamps

`bin/shelltools.php exiffixtimestamps --help`

Calculates time differences to fix photos later using `exiftool`.

**Situation:** You forgot to set the time of your digital camara because the 
batteries were empty. New batteries are now in and you start making photos.

Later you see the dates of your photos are too old. Maybe in the filename and 
probably in the exif/ metadata. If so, here you get help.

**Solution**: Make a photo now from a computer where the date/time (including 
seconds) is displayed. Make sure the time on your computer is correct!

Take the photo and look for the DateTime (e.g. DateTimeOriginal or the time of 
photo on the SD-Card) the file was created/ made.

Write it down to `--datetimeValueOld`
(Note: The format may be a bit different. By default someting like this is 
expected: '2023-12-31 23:58:59'.

Now write down the datetime of the photo/ picture you made from the computer 
to the --datetimeValueNew in the same format.

E.g. 
```
./bin/shelltools.php exiffixtimestamps \
    --test \
    --datetimeValueOld="2009-01-31 00:42:54" \
    --datetimeValueNew="2023-07-17 22:56:08"
    (futher required options...)
```

This would execute or output (depending on --test flag) the differences and 
shows you informations and the command you can execute to fix the datetime 
inside the photo. To change metadata/ exif metadata.

E.g: `exiftool '-AllDates+=14:5:17 22:13:14' DIR_OR_FILE_LOCATION`

To change the filename to that new date/time value to fix future mistakes: 
Action `exifmeta2filename` can solve this. E.g: Rename "GoPro-1234.jpg" to 
"20230717_225608.jpg"


#### Action exifmeta2filename

`./bin/shelltools.php exifmeta2filename --help`

Reads a dateime value in the exif metadata of a photo/image (maybe a video if 
supported by exiftool) file and renames the filename to given format of the 
date/time value. E.g to: `201512131_183105.jpg` or 201512131_183105-1.jpg if 
previous file already exists).

For improved sortation and organisation of photo collections, filenames 
containing the date never lose a timestamp e.g. when moving files to different 
storages. The filename or the exif metadata tells the truth. Not the timestamp 
on that disc where it is currently stored!

Start with default values:

    `./bin/shelltools.php --test exifmeta2filename`

Before you execute the exiftool command you may check the changes first.

    exiftool -d '%Y%m%d_%H%M%S' -DateTimeOriginal -S -s DIR_OR_FILE_LOCATION

Playground of real commands. This action helps in calculating:

// meta datetime 2 filename:
// exiftool '-FileName<CreateDate' -d %Y%m%d_%H%M%S%%-c.%%e /tmp/pictures/ 
// exiftool '-FileName<DateTimeOriginal' -d %Y%m%d_%H%M%S%%-c.%%e /tmp/pictures/ 
// exiftool "-DateTimeOriginal+=5:10:2 10:48:0" /tmp/pictures/
// exiftool -AllDates+=1:30 file1 file2 /tmp/pictures/
// My current task: 20090131_004254 TO: 20230717_225608 => 14Y 5M 15D 22H 13M 14sec => -AllDates+=14:5:15 22:13:14
// exiftool "-AllDates+=14:5:16 22:13:14" /tmp/pictures/


#### Action exiffilename2meta

You lost the metadata or you have really old files were exif metadata just do 
not exists, but the files contain the date/time value.

With this you can add the missing metadata based on the filename to the 
metadata. If that exists you can rename the files and still read e.g the date/
time value when it was created.



### Adapters for imagemagic:convert

#### Adapter resizeimages

It uses imagemagick package and the 'convert' command.

Make sure, if a source path is given, that only files of the same format exists.
E.g. only jpg or png images. Also video formats could be converted if exists.
Just in case to not overheat your cpu.

Resize images to a given size of pixel. E.g:
```
    bin/shelltools.php --test resizeimages \
        --source=/tmp/gopro/20120802_192919.jpg \
        --suffix=_x \
        --size=1600
    # will create: /tmp/gopro/20120802_192919_x1600.jpg
```

Example using a source path:
```
    bin/shelltools.php --test resizeimages \
        --source=/tmp/gopro \
        --suffix=_x \
        --size=1600
    # Scans all files in source and will create:
    # /tmp/gopro/[sourcefile]_x1600[.ext]
```



### Adapters for ffmpeg

ffmpeg is THE video editor/ converter for the shell. Nearly every problem can
be solved with it. Cutting, converting, resizing and much more for videos.


#### Action ffmpegcuttrimvideo

Cut a video by given start, end datetime value in different flavours. Possible
ways:
+ 'range' cut at start until stop value
+ 'duration' cat at start until given value for endtime. Were endtime is ment as
  duration and not the stop time! E.g cat at start and 10minutes long
+ 'reverse' start time in negativ form. start from the end of the file. E.g:
  Give the last 10minutes of the video



#### Adapter resizevideos

It uses ffmpeg package to resize a video by keeping the ratio! x,y axis.

Resize videos to a given size of pixel for the x-axis. E.g:
```
    bin/shelltools.php --test resizevideos \
        --source=/tmp/gopro/20120802_192919.mp4 \
        --suffix=_x \
        --size=720
    # will create: /tmp/gopro/20120802_192919_x720.mp4
```

Example using a source path:
Be sure having only the same type of files/ videos when using a `--source` as
path and not a file!
```
    bin/shelltools.php --test resizevideos \
        --source=/tmp/gopro \
        --suffix=_x \
        --size=720
    # Scans all files in source and will create:
    # /tmp/gopro/[sourcefile]_x720[.ext]
```



### General hints for ffmpeg

man ffmpeg



### General hints for exiftool and in php context

https://exiftool.org/#shift

Date/Time Shift Feature
Have you ever forgotten to set the date/time on your digital camera before
taking a bunch of pictures? ExifTool has a time shift feature that makes it
easy to apply a batch fix to the timestamps of the images (eg. change the
"Date Picture Taken" reported by Windows Explorer). Say for example that your
camera clock was reset to 2000:01:01 00:00:00 when you put in a new battery
at 2005:11:03 10:48:00. Then all of the pictures you took subsequently have
timestamps that are wrong by 5 years, 10 months, 2 days, 10 hours and 48
minutes. To fix this, put all of the images in the same directory ("DIR")
and run exiftool:

    exiftool "-DateTimeOriginal+=5:10:2 10:48:0" DIR

The example above changes only the DateTimeOriginal tag, but any writable
date or time tag can be shifted, and multiple tags may be written with a
single command line. Commonly, in JPEG images, the DateTimeOriginal,
CreateDate and ModifyDate values must all be changed. For convenience, a
Shortcut tag called AllDates has been defined to represent these three tags.
So, for example, if you forgot to set your camera clock back 1 hour at the
end of daylight savings time in the fall, you can fix the images with:

   exiftool -AllDates-=1 DIR

See Image::ExifTool::Shift.pl (download in PDF format) for details about the
syntax of the time shift string.

Note: Not all date/time information is covered by the AllDates shortcut.
Specifically, the filesystem date/time tags are not included, and this
command will reset FileModifyDate to the current date/time as it should
when the file is modified, unless either the -P option is used, or
FileModifyDate is set to something else. To shift FileModifyDate along with
the other tags, add -FileModifyDate-=1 to the command above.

If you have 'exiftool':
     man exiftool
for further infomations. This script helps in a few steps.


Other intresting scripts for huge list of files or regulary managing metadata 
see: https://github.com/tsmgeek/ExifTool_PHP_Stayopen/tree/master

The php tool getID3 also supports writing exif data to images or mp4 but its 
current state is outdated (php <= 7, php >= 5) and it seems to die or support 
will end. This program works nativ on the files (in php). 
exiftool is based on perl. like php was once. :-)
