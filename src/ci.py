#!/usr/bin/python3
import collections
import sys
import commands


def handle_dynamic_args():
    if len(sys.argv) < 2:
        raise ValueError(
            "\"{0}\" requires at least 2 arguments {1} found.".format(
                sys.argv[0],
                len(sys.argv)
            )
        )

    args = collections.deque(sys.argv.copy())
    args.popleft()

    if sys.argv[1] == "build":
        cmd = commands.Build()
        args.popleft()
    elif sys.argv[1] == "version":
        cmd = commands.Version()
        args.popleft()
    else:
        cmd = commands.Run()

    cmd.run(args)


if __name__ == "__main__":
    handle_dynamic_args()
