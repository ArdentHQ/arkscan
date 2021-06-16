#!/bin/bash
# Gr33nDrag0n / v1.2 / 2017-04-02
# tharude / v.2.5.1 / 2019-07-12

EXPLORER_NODE="$1"
if [ -z "$EXPLORER_NODE" ]; then
    EXPLORER_NODE="https://wallets.ark.io/api"
fi

SCRIPT_PATH="$( cd -- "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
OutputFile="$SCRIPT_PATH/../../public/VoteReport.txt"

###############################################################################
# FUNCTIONS
###############################################################################

function GetVotersCount {

        VotersJsonData=$( curl -s "$EXPLORER_NODE/delegates/$1/voters?page=1&limit=1" )
        echo $VotersJsonData | jq '.meta.totalCount'
}

#==============================================================================

function PrintJsonData {

        echo $1 | jq -c -r '.data[] | { rank, username, votes, address, publicKey, production }' | while read Line; do

                Rank=$( printf %02d $( echo $Line | jq -r '.rank' ) )

                Delegate=$( printf %-25s $( echo $Line | jq -r '.username' ) )
                Approval=$( printf %0.2f $( echo $Line | jq -r '.production.approval' ) )

                Vote=$( expr $( echo $Line | jq -r '.votes' ) / 100000000 )
                Vote=$( echo $Vote | sed ':a;s/\B[0-9]\{3\}\>/,&/;ta' )
                Vote=$( printf %10s $Vote )

                PublicKey=$( echo $Line | jq -r '.publicKey' )
                Voters=$( printf %4s $( GetVotersCount $PublicKey ) )

                echo "|  $Rank  | $Delegate |  $Approval  | $Vote |  $Voters  |" >> $2
        done
}
#==============================================================================

function PrintTotalVotingWeightData {

        TotalArk=$( curl -s "$EXPLORER_NODE/blockchain" | jq -r '.data.supply' )

        TotalVote=0
        TotalVoters=0
        while read Line; do

                Vote=$( echo $Line | jq -r '.votes' )
                TotalVote=$( expr $TotalVote + $Vote )

                PublicKey=$( echo $Line | jq -r '.publicKey' )
                Voters=$( GetVotersCount $PublicKey )
                TotalVoters=$( expr $TotalVoters + $Voters )

        done <<< "$( echo $1 | jq -c -r '.data[] | { rank, username, votes, address, publicKey, production }' )"

        Percentage=$( bc <<< "scale=2; $TotalVote * 100 / $TotalArk" )

        TotalVote=$( expr $( echo $TotalVote ) / 100000000 )
        TotalVote=$( echo $TotalVote | sed ':a;s/\B[0-9]\{3\}\>/,&/;ta' )

        TotalArk=$( expr $( echo $TotalArk ) / 100000000 )
        TotalArk=$( echo $TotalArk | sed ':a;s/\B[0-9]\{3\}\>/,&/;ta' )

        echo -e "Top 51 Delegates Stats\n" >> $2
        echo -e "=> Total Votes  : $Percentage% ( $TotalVote / $TotalArk )" >> $2
        echo -e "=> Total Voters : $TotalVoters\n" >> $2
}

###############################################################################
# MAIN
###############################################################################

JsonData1=$( curl -s "$EXPLORER_NODE/delegates?page=1&limit=51" )
JsonData2=$( curl -s "$EXPLORER_NODE/delegates?limit=29&offset=51" )

WorkFile='./TxtVoteReport.txt'

echo '' > $WorkFile
PrintTotalVotingWeightData $JsonData1 $WorkFile
echo '===================================================================' >> $WorkFile
echo '| Rank | Delegate                  | Vote % |  Vote ARK  | Voters |' >> $WorkFile
echo '===================================================================' >> $WorkFile
PrintJsonData $JsonData1 $WorkFile
echo '===================================================================' >> $WorkFile
PrintJsonData $JsonData2 $WorkFile
echo '===================================================================' >> $WorkFile
Date=$( date -u "+%Y-%m-%d %H:%M:%S" )
echo -e "\n $Date UTC / TxtVoteReport.sh v2.5.1 / ark.io \n" >> $WorkFile
echo -e "\n NOTE: This report is based on the 51 active delegates only! \n" >> $WorkFile

cp -f $WorkFile $OutputFile
