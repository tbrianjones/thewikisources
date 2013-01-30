# configure the host for the events index
es_host='http://ec2-54-234-115-6.compute-1.amazonaws.com:9200/'

# delete the nsns index
curl -XDELETE "${es_host}/books/?pretty=true"

# sleep to let elasticsearch delete complete
sleep=2
echo $'\n\n' "sleeping for $sleep seconds to let the index delete" $'\n\n'
sleep $sleep

# create the events index and apply settings
curl -XPUT "${es_host}/books/?pretty=true" -d '{
	"index" : {
		"number_of_shards" : 5,
		"number_of_replicas" : 1,
		"refresh_interval" : "1s"
	}
}'

# create event mapping
curl -XPOST "${es_host}/books/book/_mapping?pretty=true" -d '{
	"event" : {
		"type" : "object",
		"include_in_all" : "true",
		"index" : "analyzed",
		"path" : "full",
		"properties" : {
			"isbn_13" : { "type" : "long", "include_in_all" : "false" },
			"title" : { "type" : "string", "boost" : 5.0 },
			"subtitle" : { "type" : "string", "boost" : 3.0 },
			"categories" : { "type" : "string", "boost" : 3.0 },
			"references" : {
				"properties" : {
					"id" : { "type" : "integer", "include_in_all" : "false" },
					"context" : { "type" : "string" }
				}
			}
		}
	}
}'