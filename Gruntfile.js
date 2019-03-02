
module.exports = function(grunt) {
    'use strict';

    var config;
	if ( grunt.file.exists('config.json') ) {
		config = grunt.file.readJSON('config.json');
	} else {
		config = {
			"dropbox" : {
				"access_token" : process.env.DROPBOX_TOKEN,
				"upload_path" : process.env.DROPBOX_UPLOAD_PATH
			},
			"s3InlineDocs" : {
				"accessKeyId" : process.env.AWS_ACCESS_KEY_ID,
				"secretAccessKey" : process.env.AWS_SECRET_ACCESS_KEY,
				"bucket" : process.env.AWS_S3_BUCKET_INLINE_DOCS,
				"region" : process.env.AWS_DEFAULT_REGION
			},
			"s3UploadZip" : {
				"accessKeyId" : process.env.AWS_ACCESS_KEY_ID,
				"secretAccessKey" : process.env.AWS_SECRET_ACCESS_KEY,
				"bucket" : process.env.AWS_S3_BUCKET_UPLOAD_ZIP,
				"region" : process.env.AWS_DEFAULT_REGION
			},
			"slackUpload" : {
				"token" : process.env.SLACK_TOKEN_UPLOAD,
				"channel" : process.env.SLACK_CHANNEL_UPLOAD
			},
			"slackNotification" : {
				"token" : process.env.SLACK_NOTIFICATION_TOKEN,
				"channel" : process.env.SLACK_CHANNEL_NOTIFICATION
			}
		};
	}
    var gfVersion = '';

	var commitId = process.env.CI_COMMIT_ID ? process.env.CI_COMMIT_ID : '';

    require('matchdep').filterDev('grunt-*').forEach( grunt.loadNpmTasks );

    grunt.getPluginVersion = function( appendCommitId ){

        var p = 'gravityflow.php';
        if(gfVersion == '' && grunt.file.exists(p)){
            var source = grunt.file.read(p);
            var found = source.match(/Version:\s(.*)/);
            gfVersion = found[1];
        }

		if ( appendCommitId && commitId ) {
			return gfVersion + '-' + commitId.substring(0, 7);
		}

        return gfVersion;
    };

    grunt.getDropboxConfig = function(){
        var key = config.dropbox.upload_path;
        var obj = {};
		key += '/Releases';
		obj[key] = [ 'gravityflow_<%= grunt.getPluginVersion() %>.zip', 'gravityflow_docs_latest.zip'];
        return obj;
    };

    grunt.initConfig({

        /**
         * Generate a POT file.
         */
        makepot: {
            all: {
                options: {
                    cwd: '.',
					exclude: ['tmp/.*', 'tests/.*', 'vendor/.*'],
                    mainFile: 'gravityflow.php',
                    domainPath: 'languages',
                    potComments: 'Copyright 2015-{year} Steven Henty.',
                    potHeaders: {
                        'language-team': 'Steven Henty <support@gravityflow.io>',
                        'last-translator': 'Steven Henty <support@gravityflow.io>',
                        'report-msgid-bugs-to': 'https://www.gravityflow.io',
                        'Project-Id-Version': 'gravityflow',
                        'language': 'en_US',
                        'plural-forms': 'nplurals=2; plural=(n != 1);',
                        'x-poedit-basepath': '../',
                        'x-poedit-bookmarks': '',
                        'x-poedit-country': 'United States',
                        'x-poedit-keywordslist': true,
                        'x-poedit-searchpath-0': '.',
                        'x-poedit-sourcecharset': 'utf-8',
                        'x-textdomain-support': 'yes',
                        'x-generator' : 'Gravity Flow Build Server'
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true
                }
            }
        },

        /**
         * Minify JavaScript source files.
         */
        uglify: {
            gravityflow: {
                expand: true,
                ext: '.min.js',
                src: [
                    'js/*.js',

                    // Exclusions
                    '!js/*.min.js',
                ]
            }
        },
        /**
         * Minify CSS source files.
         */
        cssmin: {
            gravityflow: {
                expand: true,
                ext: '.min.css',
                src: [
                    'css/*.css',
                    // Exclusions
                    '!css/*.min.css',
                ]
            }
        },

        /**
         * Compression tasks
         */
        compress: {
            gravityflow: {
                options: {
                    archive: 'gravityflow_<%= grunt.getPluginVersion(true) %>.zip'
                },
                files: [
                    { src: 'includes/**', dest: 'gravityflow/' },
                    { src: 'js/**', dest: 'gravityflow/'  },
					{ src: 'fonts/**', dest: 'gravityflow/'  },
                    { src: 'css/**', dest: 'gravityflow/'  },
                    { src: 'images/**', dest: 'gravityflow/'  },
                    { src: 'languages/**', dest: 'gravityflow/'  },
                    { src: 'readme.txt', dest: 'gravityflow/'  },
                    { src: 'gravityflow.php', dest: 'gravityflow/'  },
                    { src: 'class-gravity-flow.php', dest: 'gravityflow/'  },
                    { src: 'index.php', dest: 'gravityflow/' }
                ]
            },
            docs: {
                options: {
                    archive: 'gravityflow_docs_latest.zip'
                },
                files: [
                    { src: 'docs/**' }
                ]
            }
        },

        /**
         * Cleaning - removing temp files
         */
        clean: {
            options: {
                force: true
            },
            all: [ 'apigen_tmp', 'docs', 'gravityflow_<%= grunt.getPluginVersion(true) %>.zip', 'gravityflow_docs_latest.zip' ]
        },

        /**
         * Shell commands
         */
        shell: {
            options: {
                stdout: true,
                stderr: true
            },
            apigen: {
                command: [
                    'cd vendor/bin/',
                    'export TMPDIR="' + process.cwd() + '/apigen_tmp"',
                    'php apigen generate --config="../../apigen.neon"'
                ].join('&&')
            },
            transifex:{
                command: [
                    'tx pull -a -f --minimum-perc=1'
                ].join('&&')
            }
        },

        dropbox: {
            options: {
                cwd: 'docs',
                access_token: config.dropbox.access_token
            },
            upload: {
                files: grunt.getDropboxConfig()
            }
        },

        aws_s3: {
            options: {
                uploadConcurrency: 5, // 5 simultaneous uploads
                downloadConcurrency: 5, // 5 simultaneous downloads
				accessKeyId: config.s3InlineDocs.accessKeyId,
				secretAccessKey: config.s3InlineDocs.secretAccessKey
            },
            inlinedocs: {
                options: {
                    region: config.s3InlineDocs.region,
                    bucket: config.s3InlineDocs.bucket,
                    access: 'public-read',
                    differential: true // Only uploads the files that have changed
                },
                files: [
                    {expand: true, cwd: 'docs', src: ['**'], dest: ''},
                ]
            },
			upload_zip: {
				options: {
					region: config.s3UploadZip.region,
					bucket: config.s3UploadZip.bucket,
					access: 'public-read'
				},
				files: [
					{expand: true, src: 'gravityflow_<%= grunt.getPluginVersion(true) %>.zip', dest: 'builds'},
				]
			}
        },

        potomo: {
            dist: {
                options: {
                    poDel: false
                },
                files: [{
                    expand: true,
                    cwd: 'languages',
                    src: ['*.po'],
                    dest: 'languages',
                    ext: '.mo',
                    nonull: true
                }]
            }
        },

		slack_notifier: {
			notification: {
				options: {
					token: config.slackNotification.token,
					channel: '#builds',
					text: 'New Build for Gravity Flow v' + grunt.getPluginVersion(false),
					username: 'Gravity Flow',
					as_user: false,
					link_names: true,
					attachments: [
						{
							'fallback': 'New Gravity Flow Build.',
							'color': '#36a64f',
							'pretext': '',
							'title': 'Download',
							'title_link': 'https://s3.amazonaws.com/' + config.s3UploadZip.bucket + '/builds/gravityflow_<%= grunt.getPluginVersion() %>.zip',
							'mrkdwn_in': ["pretext", "text", "fields"],
							'fields': [
								{
									'title': 'Version',
									'value': grunt.getPluginVersion(false),
									'short': true
								},
								{
									'title': 'Commit ID',
									"value": '<https://github.com/gravityflow/gravityflow/commit/' + commitId + '|' + commitId.substring(0, 7) + '>',
									'short': true
								}
							],
							'image_url': 'http://my-website.com/path/to/image.jpg',
							'thumb_url': 'http://example.com/path/to/thumb.png'
						}
					],
					unfurl_links: true,
					unfurl_media: true,
					icon_url: 'https://avatars3.githubusercontent.com/u/12782633?v=3&s=200'
				}
			}
		},

		replace: {
			dist: {
				options: {
					patterns: [
						{
							match: 'Version: ' + grunt.getPluginVersion(false),
							replacement: 'Version: ' + grunt.getPluginVersion(true),
						}
					]
				},
				files: [
					{expand: true, flatten: true, src: ['gravityflow.php'], dest: 'gravityflow.php'}
				]
			}
		},

		'string-replace': {
			inline: {
				files: {
					'./': 'gravityflow.php',
				},
				options: {
					replacements: [
						{
							pattern: 'Version: ' + grunt.getPluginVersion(false),
							replacement: 'Version: ' + grunt.getPluginVersion(true)
						}
					]
				}
			}
		}

	});

	grunt.registerTask('minimize', [ 'uglify:gravityflow', 'cssmin:gravityflow' ]);
	grunt.registerTask('translations', [ 'makepot', 'shell:transifex', 'potomo' ]);
	grunt.registerTask('default', [ 'clean', 'string-replace', 'minimize', 'translations', 'compress', 'aws_s3:upload_zip' ]);
	grunt.registerTask('build', [ 'clean', 'string-replace', 'minimize', 'translations', 'compress', 'dropbox', 'aws_s3:upload_zip', 'clean' ]);
	grunt.registerTask('publish', [ 'clean', 'minimize', 'translations', /*'shell:apigen', 'aws_s3:inlinedocs',*/ 'compress', 'dropbox', 'clean' ]);
};
